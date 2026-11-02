<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public const TYPES = [
        'course'     => 'By course',
        'department' => 'By department',
        'semester'   => 'By semester',
        'payments'   => 'Payment summary',
    ];

    public function index(Request $request)
    {
        $type       = array_key_exists((string) $request->type, self::TYPES) ? $request->type : 'course';
        $semesterId = $request->input('semester_id');

        [$headings, $rows] = match ($type) {
            'department' => $this->byDepartment($semesterId),
            'semester'   => $this->bySemester(),
            'payments'   => $this->paymentSummary($semesterId),
            default      => $this->byCourse($semesterId),
        };

        if ($request->boolean('export')) {
            ActivityLog::record('export', 'Exported report: ' . self::TYPES[$type]);

            return $this->csv($type, $headings, $rows);
        }

        return view('reports.index', [
            'types'      => self::TYPES,
            'type'       => $type,
            'semesters'  => Semester::orderByDesc('start_date')->get(),
            'semesterId' => $semesterId,
            'headings'   => $headings,
            'rows'       => $rows,
        ]);
    }

    private function byCourse($semesterId): array
    {
        $count = fn (string $status) => fn ($q) => $q->where('status', $status)
            ->when($semesterId, fn ($w) => $w->where('semester_id', $semesterId));

        $rows = Course::with('department')
            ->withCount(['enrollments as enrolled' => $count('Enrolled'), 'enrollments as waitlisted' => $count('Waitlisted')])
            ->orderBy('code')
            ->get()
            ->map(fn ($c) => [
                $c->code, $c->title, $c->department->name, $c->capacity, $c->enrolled, $c->waitlisted,
                $c->capacity ? round($c->enrolled / $c->capacity * 100) . '%' : '-',
            ])->all();

        return [['Code', 'Course', 'Department', 'Capacity', 'Enrolled', 'Waitlisted', 'Fill rate'], $rows];
    }

    private function byDepartment($semesterId): array
    {
        $data = DB::table('departments as d')
            ->leftJoin('courses as c', 'c.department_id', '=', 'd.id')
            ->leftJoin('enrollments as e', function ($join) use ($semesterId) {
                $join->on('e.course_id', '=', 'c.id')->where('e.status', 'Enrolled');
                if ($semesterId) {
                    $join->where('e.semester_id', $semesterId);
                }
            })
            ->groupBy('d.id', 'd.name', 'd.code')
            ->orderBy('d.name')
            ->select('d.name', 'd.code', DB::raw('count(distinct c.id) as courses'), DB::raw('count(e.id) as enrolled'))
            ->get()
            ->map(fn ($r) => [$r->name, $r->code, $r->courses, $r->enrolled])
            ->all();

        return [['Department', 'Code', 'Courses', 'Enrolled students'], $data];
    }

    private function bySemester(): array
    {
        $paid = Payment::where('payments.status', 'Paid')
            ->join('enrollments', 'enrollments.id', '=', 'payments.enrollment_id')
            ->groupBy('enrollments.semester_id')
            ->selectRaw('enrollments.semester_id as sid, sum(payments.amount) as total')
            ->pluck('total', 'sid');

        $rows = Semester::withCount([
                'enrollments as enrolled'   => fn ($q) => $q->where('status', 'Enrolled'),
                'enrollments as waitlisted' => fn ($q) => $q->where('status', 'Waitlisted'),
                'enrollments as dropped'    => fn ($q) => $q->where('status', 'Dropped'),
            ])
            ->orderByDesc('start_date')
            ->get()
            ->map(fn ($s) => [
                $s->name . ($s->is_active ? ' (active)' : ''),
                $s->start_date->format('Y-m-d') . ' to ' . $s->end_date->format('Y-m-d'),
                $s->enrolled, $s->waitlisted, $s->dropped,
                number_format((float) ($paid[$s->id] ?? 0), 2),
            ])->all();

        return [['Semester', 'Dates', 'Enrolled', 'Waitlisted', 'Dropped', 'Paid ($)'], $rows];
    }

    private function paymentSummary($semesterId): array
    {
        $rows = Payment::query()
            ->when($semesterId, fn ($q) => $q->whereHas('enrollment', fn ($e) => $e->where('semester_id', $semesterId)))
            ->selectRaw('method, status, count(*) as n, sum(amount) as total')
            ->groupBy('method', 'status')
            ->orderBy('method')->orderBy('status')
            ->get()
            ->map(fn ($r) => [$r->method, $r->status, $r->n, number_format((float) $r->total, 2)])
            ->all();

        return [['Method', 'Status', 'Transactions', 'Total ($)'], $rows];
    }

    private function csv(string $type, array $headings, array $rows)
    {
        $name = 'report-' . $type . '-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");                       // so Excel reads UTF-8 correctly
            fputcsv($out, $headings, ',', '"', '');
            foreach ($rows as $row) {
                fputcsv($out, $row, ',', '"', '');
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

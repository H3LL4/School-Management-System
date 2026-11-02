<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['enrollment.student', 'enrollment.course', 'enrollment.semester'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('reference', 'like', $term)
                    ->orWhereHas('enrollment.student', fn ($s) => $s->where('name', 'like', $term)));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('method'), fn ($q) => $q->where('method', $request->method))
            ->when($request->filled('semester_id'), fn ($q) => $q->whereHas('enrollment', fn ($e) => $e->where('semester_id', $request->semester_id)));

        $totalPaid = (clone $query)->where('status', 'Paid')->sum('amount');

        return view('payments.index', [
            'payments'  => $query->latest('id')->paginate(10)->withQueryString(),
            'semesters' => Semester::orderByDesc('start_date')->get(),
            'totalPaid' => (float) $totalPaid,
        ]);
    }

    public function create(Request $request)
    {
        return view('payments.form', [
            'payment'     => new Payment(['paid_at' => today(), 'status' => 'Paid', 'method' => 'Cash', 'enrollment_id' => $request->query('enrollment_id')]),
            'enrollments' => $this->payableEnrollments(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enrollment_id' => ['required', Rule::exists('enrollments', 'id')->where('status', 'Enrolled')],
            ...$this->commonRules(),
        ]);

        $this->assertWithinBalance(Enrollment::findOrFail($data['enrollment_id']), $data);

        Payment::create($data);

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }

    public function edit(Payment $payment)
    {
        $payment->load('enrollment.student', 'enrollment.course', 'enrollment.semester');

        return view('payments.form', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate($this->commonRules());

        $this->assertWithinBalance($payment->enrollment, $data, $payment);

        $payment->update($data);

        return redirect()->route('payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }

    private function commonRules(): array
    {
        return [
            'amount'  => ['required', 'numeric', 'gt:0', 'max:99999'],
            'method'  => ['required', Rule::in(Payment::METHODS)],
            'status'  => ['required', Rule::in(Payment::STATUSES)],
            'paid_at' => ['required', 'date'],
            'note'    => ['nullable', 'string', 'max:255'],
        ];
    }

    /** Enrolled students only, each with what they owe and what they have paid so far. */
    private function payableEnrollments()
    {
        return Enrollment::with(['student', 'course', 'semester'])
            ->where('status', 'Enrolled')
            ->withSum(['payments as paid_total' => fn ($q) => $q->where('status', 'Paid')], 'amount')
            ->get()
            ->sortBy(fn ($e) => $e->student->name)
            ->values();
    }

    /** A "Paid" payment cannot push the total above the course tuition fee. */
    private function assertWithinBalance(Enrollment $enrollment, array $data, ?Payment $ignore = null): void
    {
        if ($data['status'] !== 'Paid') {
            return;
        }

        $paid = $enrollment->payments()
            ->where('status', 'Paid')
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
            ->sum('amount');

        $remaining = max(0, (float) $enrollment->course->tuition_fee - (float) $paid);

        if ((float) $data['amount'] > $remaining + 0.00001) {
            throw ValidationException::withMessages([
                'amount' => 'Amount is more than the remaining balance of $' . number_format($remaining, 2) . '.',
            ]);
        }
    }
}

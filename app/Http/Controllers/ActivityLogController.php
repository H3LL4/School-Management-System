<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('username', 'like', $term)->orWhere('description', 'like', $term));
            })
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->action))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('activity.index', [
            'logs'    => $logs,
            'actions' => ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}

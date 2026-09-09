<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\QueueCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * Served tickets (queue_calls) with staff and date/time. Admin only.
     */
    public function index(Request $request)
    {
        $query = DB::table('queue_calls')
            ->leftJoin('users', 'users.window_id', '=', 'queue_calls.window_id')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('windows', 'queue_calls.window_id', '=', 'windows.id')
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->select(
                'queue_calls.id as queue_call_id',
                'queues.id as queue_id',
                'queues.queue_number',
                'services.service_name',
                'windows.window_name',
                'users.name as staff_name',
                'queue_calls.called_time',
                'queue_calls.finished_time'
            )
            ->orderByDesc('queue_calls.called_time');

        if ($request->filled('date')) {
            $query->whereDate('queue_calls.called_time', $request->input('date'));
        }

        $records = $query->paginate(20);

        return view('admin.history', compact('records'));
    }

    /**
     * All generated tickets (queues). Admin only.
     */
    public function tickets(Request $request)
    {
        $query = Queue::query()
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->leftJoin('students', 'students.student_id', '=', 'queues.student_id')
            ->select('queues.*', 'services.service_name', 'students.name as student_name')
            ->orderByDesc('queues.queue_date')
            ->orderByDesc('queues.id');

        if ($request->filled('date')) {
            $query->whereDate('queues.queue_date', $request->input('date'));
        }
        if ($request->filled('status')) {
            $query->where('queues.status', $request->input('status'));
        }

        $records = $query->paginate(20);

        return view('admin.history-tickets', compact('records'));
    }

    /**
     * System reports and analytics. Admin only.
     */
    public function reports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $latestFinishedCall = 'queue_calls.id = (SELECT MAX(qc2.id) FROM queue_calls qc2 WHERE qc2.queue_id = queues.id AND qc2.finished_time IS NOT NULL)';

        $servedCount = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->where('queues.status', 'done')
            ->whereRaw($latestFinishedCall)
            ->whereBetween(DB::raw('DATE(called_time)'), [$dateFrom, $dateTo])
            ->distinct()
            ->count('queues.id');

        $byService = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->where('queues.status', 'done')
            ->whereRaw($latestFinishedCall)
            ->whereBetween(DB::raw('DATE(queue_calls.called_time)'), [$dateFrom, $dateTo])
            ->select('services.service_name', DB::raw('COUNT(*) as total'))
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('total')
            ->get();

        $avgServiceTime = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->where('queues.status', 'done')
            ->whereRaw($latestFinishedCall)
            ->whereBetween(DB::raw('DATE(called_time)'), [$dateFrom, $dateTo])
            ->whereNotNull('finished_time')
            ->whereRaw('TIMESTAMPDIFF(SECOND, called_time, finished_time) BETWEEN 0 AND 28800')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, called_time, finished_time)) as avg_seconds')
            ->value('avg_seconds');

        $avgWaitingTime = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->where('queues.status', 'done')
            ->whereRaw($latestFinishedCall)
            ->whereBetween(DB::raw('DATE(queue_calls.called_time)'), [$dateFrom, $dateTo])
            ->whereNotNull('queues.created_at')
            ->whereRaw('TIMESTAMPDIFF(SECOND, queues.created_at, queue_calls.called_time) BETWEEN 0 AND 28800')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, queues.created_at, queue_calls.called_time)) as avg_seconds')
            ->value('avg_seconds');

        $byWindow = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('windows', 'queue_calls.window_id', '=', 'windows.id')
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->leftJoin('users', function ($join) {
                $join->on('users.window_id', '=', 'queue_calls.window_id')
                    ->where('users.role', '=', 'staff');
            })
            ->where('queues.status', 'done')
            ->whereRaw($latestFinishedCall)
            ->whereBetween(DB::raw('DATE(queue_calls.called_time)'), [$dateFrom, $dateTo])
            ->select(
                'queue_calls.window_id',
                'windows.window_name',
                'services.service_name',
                DB::raw('COALESCE(MAX(users.name), windows.window_name) as staff_name'),
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(CASE
                    WHEN queue_calls.finished_time IS NOT NULL
                     AND TIMESTAMPDIFF(SECOND, queue_calls.called_time, queue_calls.finished_time) BETWEEN 0 AND 28800
                    THEN TIMESTAMPDIFF(SECOND, queue_calls.called_time, queue_calls.finished_time)
                END) as avg_service_seconds'),
                DB::raw('AVG(CASE
                    WHEN queues.created_at IS NOT NULL
                     AND TIMESTAMPDIFF(SECOND, queues.created_at, queue_calls.called_time) BETWEEN 0 AND 28800
                    THEN TIMESTAMPDIFF(SECOND, queues.created_at, queue_calls.called_time)
                END) as avg_wait_seconds')
            )
            ->groupBy(
                'queue_calls.window_id',
                'windows.window_name',
                'services.service_name'
            )
            ->orderBy('windows.window_name')
            ->get();

        return view('admin.history-reports', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'servedCount' => $servedCount,
            'byService' => $byService,
            'byWindow' => $byWindow,
            'avgServiceTimeSeconds' => $avgServiceTime !== null ? (int) round($avgServiceTime) : null,
            'avgWaitingTimeSeconds' => $avgWaitingTime !== null ? (int) round($avgWaitingTime) : null,
        ]);
    }

    public function edit(QueueCall $queue_call)
    {
        $record = DB::table('queue_calls')
            ->leftJoin('users', 'users.window_id', '=', 'queue_calls.window_id')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('windows', 'queue_calls.window_id', '=', 'windows.id')
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->where('queue_calls.id', $queue_call->id)
            ->select(
                'queue_calls.id as queue_call_id',
                'queue_calls.called_time',
                'queue_calls.finished_time',
                'queues.queue_number',
                'services.service_name',
                'users.name as staff_name'
            )
            ->first();

        if (! $record) {
            abort(404);
        }

        return view('admin.history-edit', ['record' => $record, 'queue_call' => $queue_call]);
    }

    public function update(Request $request, QueueCall $queue_call)
    {
        $validated = $request->validate([
            'called_time' => 'required|date',
            'finished_time' => 'nullable|date|after_or_equal:called_time',
        ]);

        $queue_call->called_time = $validated['called_time'];
        $queue_call->finished_time = $validated['finished_time'] ?: null;
        $queue_call->save();

        return redirect()->route('admin.history')->with('status', 'Record updated.');
    }

    public function destroy(QueueCall $queue_call)
    {
        $queue_call->delete();
        return redirect()->route('admin.history')->with('status', 'Record deleted.');
    }
}

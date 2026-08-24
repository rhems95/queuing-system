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
            ->select('queues.*', 'services.service_name')
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

        $servedCount = DB::table('queue_calls')
            ->whereBetween(DB::raw('DATE(called_time)'), [$dateFrom, $dateTo])
            ->count();

        $byService = DB::table('queue_calls')
            ->join('queues', 'queue_calls.queue_id', '=', 'queues.id')
            ->join('services', 'queues.service_id', '=', 'services.id')
            ->whereBetween(DB::raw('DATE(queue_calls.called_time)'), [$dateFrom, $dateTo])
            ->select('services.service_name', DB::raw('COUNT(*) as total'))
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('total')
            ->get();

        $byStaff = DB::table('queue_calls')
            ->leftJoin('users', 'users.window_id', '=', 'queue_calls.window_id')
            ->join('windows', 'queue_calls.window_id', '=', 'windows.id')
            ->whereBetween(DB::raw('DATE(queue_calls.called_time)'), [$dateFrom, $dateTo])
            ->select(
                'queue_calls.window_id',
                DB::raw('COALESCE(MAX(users.name), MAX(windows.window_name)) as staff_or_window'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('queue_calls.window_id')
            ->orderByDesc('total')
            ->get();

        $avgServiceTime = DB::table('queue_calls')
            ->whereBetween(DB::raw('DATE(called_time)'), [$dateFrom, $dateTo])
            ->whereNotNull('finished_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, called_time, finished_time)) as avg_seconds')
            ->value('avg_seconds');

        return view('admin.history-reports', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'servedCount' => $servedCount,
            'byService' => $byService,
            'byStaff' => $byStaff,
            'avgServiceTimeSeconds' => $avgServiceTime ? (int) round($avgServiceTime) : null,
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

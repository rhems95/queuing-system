<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Window;
use App\Services\FairQueueScheduler;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DisplayController extends Controller
{
    public function __construct(
        private QueueService $queueService,
        private FairQueueScheduler $scheduler,
    ) {
    }

    /**
     * Data used for both the HTML view and the JSON poll.
     */
    private function getDisplayData(): array
    {
        $today = Carbon::today()->toDateString();

        $windows = Window::query()
            ->orderBy('id')
            ->get();

        $grouped = $windows
            ->groupBy(fn ($w) => trim((string) ($w->group_name ?? '')));

        $preferredGroupOrder = ['Window 1', 'Window 2', 'Window 3', 'Window 4'];
        $orderedGroupNames = collect($preferredGroupOrder)
            ->merge($grouped->keys()->all())
            ->unique()
            ->values()
            ->all();

        $nowServingGroups = [];
        foreach ($orderedGroupNames as $groupName) {
            if (! $grouped->has($groupName)) {
                continue;
            }
            $groupWindows = $grouped->get($groupName);
            $label = $groupName !== '' ? $groupName : 'Counters';
            // Keep a stable left-to-right order (by window id) so the display does not reshuffle on poll.
            $sortedWindows = $groupWindows->sortBy('id')->values();
            $items = [];
            foreach ($sortedWindows as $window) {
                $call = $this->queueService->latestCallForWindow($window->id, $today);
                $queueNumber = $this->queueService->queueNumberFromCall($call);

                $items[] = [
                    'window_name' => $window->window_name,
                    'queue_number' => $queueNumber,
                    'call_token' => $queueNumber ? ($call->id.'|'.($call->called_time ?? '')) : null,
                ];
            }

            $nowServingGroups[] = [
                'group_name' => $label,
                'windows' => $items,
            ];
        }

        $waitingColumns = [
            'Cashier' => [],
            'N/A' => [],
            'DMO' => [],
            'Registrar' => [],
        ];

        // Same 2 Priority → 1 Regular order used by Call Next (per service).
        foreach (Service::query()->orderBy('id')->get() as $service) {
            $column = $this->waitingColumnForService((string) $service->service_name);
            if ($column === null) {
                continue;
            }

            $ordered = $this->scheduler->orderedWaiting((int) $service->id, $today, 10);
            foreach ($ordered as $q) {
                $priorityLabel = $q->priority ? 'priority' : 'regular';
                $waitingColumns[$column][] = $q->queue_number.'('.$priorityLabel.')';
            }
        }

        return [
            'now_serving_groups' => $nowServingGroups,
            'waiting_columns' => $waitingColumns,
        ];
    }

    /**
     * Map a service name to a waiting-list column header.
     */
    private function waitingColumnForService(string $serviceName): ?string
    {
        $name = strtolower($serviceName);

        if (str_contains($name, 'cash')) {
            return 'Cashier';
        }
        if (str_contains($name, 'promissory')) {
            return 'N/A';
        }
        if (str_contains($name, 'dmo') || str_contains($name, 'data management')) {
            return 'DMO';
        }
        if (str_contains($name, 'registrar')) {
            return 'Registrar';
        }

        return null;
    }

    public function index()
    {
        $data = $this->getDisplayData();

        return view('display.index', [
            'nowServingGroups' => $data['now_serving_groups'],
            'waitingColumns' => $data['waiting_columns'],
        ]);
    }

    /**
     * JSON endpoint for display page auto-refresh (polling).
     */
    public function data(): JsonResponse
    {
        return response()->json($this->getDisplayData());
    }
}

@extends('layouts.app')

@section('title', 'Kiosk')

@section('content')
    <div class="kiosk-shell max-w-6xl mx-auto">
        <div class="text-center kiosk-title-block">
            <h1 class="kiosk-title font-extrabold tracking-wide text-blue-950">KIOSK QUEUE TICKET SYSTEM</h1>
            <p id="stepIndicator" class="kiosk-step-indicator text-gray-600 tracking-widest">STEP 1 OF 3 - SELECT SERVICE</p>
        </div>

        @if ($errors->any())
            <div class="mb-3 text-red-700 text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-2">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="kioskForm" method="POST" action="{{ route('kiosk.store') }}" class="kiosk-form space-y-4">
            @csrf

            <input type="hidden" name="service_id" id="serviceIdInput" value="{{ old('service_id') }}">
            <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority', 'regular') }}">

            <div id="step1" class="step1-wrap">
                <div class="step-pill">STEP 1</div>
                <div class="step1-head">
                    <div class="step1-head-title">GET YOUR QUEUE NUMBER</div>
                    <div class="step1-head-sub">Please select the service you need</div>
                    <div class="step1-head-icon">🖥️</div>
                </div>
                <div class="step1-section-title">SELECT A SERVICE</div>
                <div class="step1-body">
                    @foreach($services as $service)
                        @php
                            $serviceName = strtolower($service->service_name);
                            $isCashier = str_contains($serviceName, 'cash');
                            $isRegistrar = str_contains($serviceName, 'registrar');
                            $isDmo = str_contains($serviceName, 'dmo') || str_contains($serviceName, 'data management');

                            $theme = $isCashier ? 'cashier' : ($isRegistrar ? 'registrar' : ($isDmo ? 'dmo' : 'default'));
                            $cardClass = 'service-card ' . $theme;
                            $iconClass = 'service-icon ' . $theme;
                            $ctaClass = 'service-cta ' . $theme;
                            $titleClass = 'service-title ' . $theme;
                            $ctaText = $isDmo ? 'DMO' : strtoupper($service->service_name);

                            $icon = $isCashier ? '💳' : ($isRegistrar ? '📝' : ($isDmo ? '🗂️' : '📌'));
                            $desc = $isCashier
                                ? 'Payment and cash related transactions'
                                : ($isRegistrar
                                    ? 'Enrollment and records related services'
                                    : ($isDmo
                                        ? '(Data Management Office)'
                                        : 'General service'));
                        @endphp
                        <button
                            type="button"
                            class="service-btn {{ $cardClass }}"
                            data-service-id="{{ $service->id }}"
                            data-service-name="{{ strtoupper($service->service_name) }}"
                        >
                            <div class="{{ $iconClass }}">{{ $icon }}</div>
                            <div class="{{ $titleClass }}">{{ strtoupper($service->service_name) }}</div>
                            <div class="service-desc">{{ $desc }}</div>
                            <div class="{{ $ctaClass }}">{{ $ctaText }}</div>
                        </button>
                    @endforeach
                </div>
                <div class="step1-footer-note">Touch a service to proceed</div>
            </div>

            <div id="step2" class="hidden step2-wrap step2-registrar">
                <div class="step-pill step2-pill">STEP 2</div>
                <div class="step2-head">
                    <div class="step2-head-icon">🎫</div>
                    <div class="step2-head-copy">
                        <p class="step2-head-title">PRIORITY &amp; TICKET</p>
                        <p class="step2-head-sub">Select priority, then print your ticket</p>
                    </div>
                </div>
                <div class="step2-body">
                    <label class="block text-2xl font-semibold mb-2 text-slate-900">Are you a priority customer?</label>
                    <div class="grid grid-cols-1 gap-3 mb-4">
                        <button type="button" class="priority-btn priority-regular priority-selected border-2 rounded-xl px-5 py-3 font-semibold text-left" data-priority="regular">
                            <div class="font-bold text-4xl leading-none">Regular</div>
                        </button>
                        <button type="button" class="priority-btn priority-priority border-2 rounded-xl px-5 py-3 font-semibold text-left" data-priority="priority">
                            <div class="font-bold text-4xl leading-none">Priority</div>
                            <div class="text-lg text-orange-700 font-normal mt-1">Senior, PWD, Pregnant, Parent/Guardian, etc.</div>
                        </button>
                    </div>

                    <div class="step2-actions flex justify-center gap-3 mt-2">
                        <button type="button" id="backToStep1" class="step2-back-btn">Back</button>
                        <button type="button" id="goToStep3" class="step2-print-btn text-white py-3 rounded-xl font-semibold px-12 text-2xl">
                            Continue
                        </button>
                    </div>
                </div>
            </div>

            <div id="step3" class="hidden step3-wrap">
                <div class="step-pill step3-pill">STEP 3</div>
                <div class="step3-head" aria-hidden="true"></div>
                <div class="step3-body">
                    <h3 class="step3-title">CONFIRM YOUR SELECTION</h3>
                    <p class="step3-label">You selected:</p>
                    <div id="confirmServiceName" class="step3-service-name">---</div>
                    <p class="step3-priority">Priority: <span id="confirmPriorityLabel" class="step3-priority-value">Regular</span></p>

                    <div id="confirmQueueInfo" class="step3-queue-info" aria-live="polite">
                        <p class="step3-info-line">Currently Serving: <strong id="confirmServing">—</strong></p>
                        <p class="step3-info-line">Priority Waiting: <strong id="confirmPriorityWaiting">0</strong></p>
                        <p class="step3-info-line">Regular Waiting: <strong id="confirmRegularWaiting">0</strong></p>
                        <p class="step3-eta" id="confirmEtaLine">Estimated Waiting Time: <strong id="confirmEta">Calculating…</strong></p>
                    </div>

                    <p class="step3-question">Do you want to print your ticket?</p>

                    <div class="step3-actions">
                        <button type="button" id="backToStep2" class="step3-back-btn">Back</button>
                        <button type="submit" class="step3-confirm-btn">Confirm &amp; Print</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var step1 = document.getElementById('step1');
            var step2 = document.getElementById('step2');
            var step3 = document.getElementById('step3');
            var stepIndicator = document.getElementById('stepIndicator');
            var serviceIdInput = document.getElementById('serviceIdInput');
            var priorityInput = document.getElementById('priorityInput');
            var confirmServiceName = document.getElementById('confirmServiceName');
            var confirmPriorityLabel = document.getElementById('confirmPriorityLabel');
            var selectedServiceName = '---';
            var estimateUrl = @json(route('kiosk.estimate'));

            function showStep(stepNo) {
                step1.classList.add('hidden');
                step2.classList.add('hidden');
                step3.classList.add('hidden');

                if (stepNo === 1) {
                    step1.classList.remove('hidden');
                    stepIndicator.textContent = 'STEP 1 OF 3 - SELECT SERVICE';
                } else if (stepNo === 2) {
                    step2.classList.remove('hidden');
                    stepIndicator.textContent = 'STEP 2 OF 3 - SELECT PRIORITY';
                } else {
                    step3.classList.remove('hidden');
                    stepIndicator.textContent = 'STEP 3 OF 3 - CONFIRM & PRINT';
                }
            }

            function setEstimateLoading() {
                var serving = document.getElementById('confirmServing');
                var pWait = document.getElementById('confirmPriorityWaiting');
                var rWait = document.getElementById('confirmRegularWaiting');
                var eta = document.getElementById('confirmEta');
                if (serving) serving.textContent = '—';
                if (pWait) pWait.textContent = '—';
                if (rWait) rWait.textContent = '—';
                if (eta) eta.textContent = 'Calculating…';
            }

            function loadEstimate() {
                setEstimateLoading();
                if (!serviceIdInput.value) return;

                var params = new URLSearchParams();
                params.set('service_id', serviceIdInput.value);
                params.set('priority', priorityInput.value || 'regular');

                fetch(estimateUrl + '?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        var serving = document.getElementById('confirmServing');
                        var pWait = document.getElementById('confirmPriorityWaiting');
                        var rWait = document.getElementById('confirmRegularWaiting');
                        var eta = document.getElementById('confirmEta');
                        if (serving) serving.textContent = data.currently_serving || 'None';
                        if (pWait) pWait.textContent = String(data.priority_waiting ?? 0);
                        if (rWait) rWait.textContent = String(data.regular_waiting ?? 0);
                        if (eta) {
                            if (data.estimated_minutes != null) {
                                eta.textContent = 'Approximately ' + data.estimated_minutes + ' minutes';
                            } else {
                                eta.textContent = 'Not available yet';
                            }
                        }
                    })
                    .catch(function () {
                        var eta = document.getElementById('confirmEta');
                        if (eta) eta.textContent = 'Not available yet';
                    });
            }

            document.querySelectorAll('.service-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    serviceIdInput.value = btn.dataset.serviceId;
                    selectedServiceName = btn.dataset.serviceName || '---';
                    var isCashier = btn.classList.contains('cashier');
                    step2.classList.remove('step2-cashier', 'step2-registrar');
                    step2.classList.add(isCashier ? 'step2-cashier' : 'step2-registrar');
                    showStep(2);
                });
            });

            document.querySelectorAll('.priority-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var selected = btn.dataset.priority;
                    priorityInput.value = selected;
                    document.querySelectorAll('.priority-btn').forEach(function (other) {
                        other.classList.remove('priority-selected');
                    });
                    btn.classList.add('priority-selected');
                });
            });

            document.getElementById('goToStep3').addEventListener('click', function () {
                if (!serviceIdInput.value) {
                    showStep(1);
                    return;
                }

                var priorityLabel = priorityInput.value === 'priority' ? 'Priority' : 'Regular';
                confirmServiceName.textContent = selectedServiceName;
                confirmPriorityLabel.textContent = priorityLabel;
                showStep(3);
                loadEstimate();
            });

            document.getElementById('backToStep1').addEventListener('click', function () {
                showStep(1);
            });

            document.getElementById('backToStep2').addEventListener('click', function () {
                showStep(2);
            });
        });
    </script>

    <style>
        /* Room for floating STEP pills so they are not clipped */
        .kiosk-shell {
            padding-top: 4px;
        }
        .kiosk-title {
            font-size: 1.75rem;
            line-height: 1.15;
        }
        .kiosk-step-indicator {
            font-size: 0.7rem;
            margin-top: 0.2rem;
        }
        .kiosk-title-block {
            margin-bottom: 0.35rem;
        }
        .kiosk-form {
            padding-top: 14px;
        }
        .kiosk-main {
            max-width: 1024px;
        }

        .step1-wrap,
        .step2-wrap,
        .step3-wrap {
            position: relative;
            z-index: 20;
            overflow: visible;
        }
        .step1-wrap.hidden,
        .step2-wrap.hidden,
        .step3-wrap.hidden {
            display: none !important;
        }

        .step1-wrap {
            background: #f4f7fb;
            border: 1px solid #d9dee7;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }
        .step-pill {
            position: absolute;
            top: -16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 50;
            background: linear-gradient(135deg, #2e7be6, #204fb7);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.04em;
            border-radius: 999px;
            padding: 6px 28px;
            font-size: 13px;
            line-height: 1.2;
            white-space: nowrap;
            border: 2px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 6px 14px rgba(33, 93, 194, 0.35);
        }
        .step1-head {
            position: relative;
            z-index: 25;
            color: #fff;
            text-align: center;
            padding: 18px 16px 12px;
            background: linear-gradient(120deg, #1e88ea, #1f43ad);
            border-radius: 20px 20px 0 0;
            overflow: hidden;
        }
        .step1-head-title {
            font-size: 28px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 0.01em;
        }
        .step1-head-sub {
            margin-top: 4px;
            font-size: 14px;
            color: #dbeafe;
        }
        .step1-head-icon {
            position: absolute;
            right: 16px;
            top: 12px;
            width: 52px;
            height: 52px;
            border-radius: 999px;
            background: #ecf4ff;
            color: #1f4ca8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: inset 0 0 0 2px #d3e3ff;
        }
        .step1-body {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            padding: 12px 14px;
        }
        .step1-section-title {
            margin: 4px 14px 0;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: #334155;
            letter-spacing: 0.02em;
        }
        .step1-footer-note {
            text-align: center;
            color: #475569;
            font-size: 13px;
            font-weight: 500;
            padding: 2px 14px 10px;
        }
        .service-card {
            background: #fff;
            border: 2px solid #d8e3f2;
            border-radius: 16px;
            padding: 14px 12px 12px;
            text-align: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 10px 14px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 210px;
        }
        .service-card:hover { transform: translateY(-2px); }
        .service-card.cashier { border-bottom: 6px solid #2196f3; }
        .service-card.registrar {
            border-color: #bce8d7;
            border-bottom: 6px solid #11a36a;
        }
        .service-card.dmo {
            border-color: #b9ecef;
            border-bottom: 6px solid #1fa8b8;
        }
        .service-card.default {
            border-color: #d6d6e4;
            border-bottom: 6px solid #6b7280;
        }
        .service-icon {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }
        .service-icon.cashier { background: #d8ecff; color: #1f62c9; }
        .service-icon.registrar { background: #d9f5e7; color: #0f8b5f; }
        .service-icon.dmo { background: #d7f5f7; color: #0f8f9b; }
        .service-icon.default { background: #e8e8ef; color: #4b5563; }
        .service-title {
            font-size: 22px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 0.02em;
        }
        .service-title.cashier { color: #1e3a8a; }
        .service-title.registrar { color: #14532d; }
        .service-title.dmo { color: #0f4c81; }
        .service-title.default { color: #374151; }
        .service-desc {
            margin-top: 4px;
            font-size: 12px;
            line-height: 1.25;
            color: #374151;
            font-weight: 500;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .service-cta {
            margin-top: auto;
            width: 100%;
            border-radius: 8px;
            color: #fff;
            font-size: 20px;
            font-weight: 800;
            padding: 7px 8px;
            line-height: 1.05;
            letter-spacing: 0.02em;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.3), 0 3px 8px rgba(0, 0, 0, 0.14);
        }
        .service-cta.cashier { background: linear-gradient(90deg, #3c95ee, #2d67d8); }
        .service-cta.registrar { background: linear-gradient(90deg, #58cdbd, #2faeaf); }
        .service-cta.dmo { background: linear-gradient(90deg, #7bd1d9, #33a7bb); }
        .service-cta.default { background: linear-gradient(90deg, #9ca3af, #6b7280); }
        .service-btn { cursor: pointer; }

        .step2-wrap {
            border-radius: 20px;
            border: 1px solid #c9dfcf;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            background: #eef8ec;
        }
        .step2-pill { top: -16px; }
        .step2-head {
            border-radius: 20px 20px 0 0;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: white;
            overflow: hidden;
        }
        .step2-head-icon {
            width: 54px;
            height: 54px;
            border-radius: 999px;
            background: #ecfff4;
            color: #13795b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.5);
            flex-shrink: 0;
        }
        .step2-head-title {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.05;
            margin: 0;
        }
        .step2-head-sub {
            font-size: 14px;
            color: #ddffef;
            margin: 2px 0 0;
        }
        .step2-body {
            padding: 16px 18px;
            background: #f3fbef;
            border-radius: 0 0 20px 20px;
        }
        .step2-actions { flex-wrap: wrap; }
        .step2-back-btn {
            min-width: 180px;
            border-radius: 10px;
            background: #e2e8f0;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            padding: 10px 14px;
        }
        .step2-print-btn {
            min-width: 180px;
            border-radius: 10px;
            background: linear-gradient(90deg, #1f6dd6, #1d4ed8);
            border: 1px solid #1d4ed8;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            padding: 10px 14px;
        }
        .step2-cashier {
            border-color: #b9d4ff;
            background: #edf4ff;
        }
        .step2-cashier .step2-head { background: linear-gradient(120deg, #2f7de6, #1f4db6); }
        .step2-cashier .step2-pill { background: linear-gradient(135deg, #2e7be6, #204fb7); }
        .step2-cashier .step2-head-sub { color: #deecff; }
        .step2-cashier .step2-body { background: #f1f6ff; }
        .step2-cashier .step2-print-btn {
            background: linear-gradient(90deg, #1f6dd6, #1d4ed8);
            border-color: #1d4ed8;
        }
        .step2-registrar .step2-head { background: linear-gradient(120deg, #169c63, #13784f); }
        .step2-registrar .step2-pill { background: linear-gradient(135deg, #169c63, #13784f); }
        .step2-registrar .step2-body { background: #f3fbef; }

        .step3-wrap {
            max-width: 720px;
            margin-left: auto;
            margin-right: auto;
            border-radius: 20px;
            border: 1px solid #d9dee7;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            background: #f8fafc;
        }
        .step3-pill {
            top: -16px;
            background: linear-gradient(135deg, #1f6dd6, #1d4ed8);
        }
        .step3-head {
            background: linear-gradient(90deg, #1f6dd6, #1d4ed8);
            color: #fff;
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            padding: 10px 12px;
            letter-spacing: 0.02em;
            border-radius: 20px 20px 0 0;
            overflow: hidden;
            min-height: 10px;
        }
        .step3-body {
            padding: 14px 14px 16px;
            text-align: center;
        }
        .step3-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e5daa;
            line-height: 1.1;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .step3-label {
            color: #475569;
            font-size: 16px;
            margin-bottom: 4px;
        }
        .step3-service-name {
            background: linear-gradient(90deg, #4b9bec, #3b82f6);
            color: #fff;
            font-weight: 900;
            font-size: 32px;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 6px;
            letter-spacing: 0.04em;
        }
        .step3-priority {
            font-size: 15px;
            color: #334155;
            margin-bottom: 2px;
        }
        .step3-priority-value { font-weight: 800; }
        .step3-queue-info {
            margin: 10px auto 8px;
            max-width: 420px;
            padding: 10px 12px;
            border: 1px solid #dbe3f0;
            border-radius: 10px;
            background: #fff;
            text-align: left;
        }
        .step3-info-line {
            margin: 0 0 4px;
            font-size: 15px;
            color: #334155;
        }
        .step3-info-line strong {
            color: #0f5fb8;
            font-weight: 800;
        }
        .step3-eta {
            margin: 8px 0 0;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            font-size: 16px;
            color: #1e3a5f;
            font-weight: 700;
        }
        .step3-eta strong {
            color: #0f5fb8;
            font-weight: 900;
        }
        .step3-question {
            font-size: 18px;
            color: #334155;
            margin-bottom: 10px;
        }
        .step3-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
        }
        .step3-back-btn,
        .step3-confirm-btn {
            min-width: 180px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 800;
            padding: 10px 14px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .step3-back-btn {
            background: #e2e8f0;
            border: 1px solid #cbd5e1;
            color: #334155;
        }
        .step3-confirm-btn {
            background: linear-gradient(90deg, #1f6dd6, #1d4ed8);
            border: 1px solid #1d4ed8;
            color: #fff;
        }
        .priority-regular {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            transition: transform 0.12s ease, background 0.12s ease, border-color 0.12s ease;
        }
        .priority-priority {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            transition: transform 0.12s ease, background 0.12s ease, border-color 0.12s ease;
        }
        .priority-btn:hover { transform: translateY(-1px); }
        .priority-regular:hover {
            background: #edf4ff;
            border-color: #2f7de6;
        }
        .priority-priority:hover {
            background: #fff7ed;
            border-color: #fb923c;
        }
        .priority-regular.priority-selected {
            background: #edf4ff;
            border-color: #2f7de6;
            box-shadow: 0 10px 16px rgba(47, 125, 230, 0.18);
        }
        .priority-priority.priority-selected {
            background: #fff7ed;
            border-color: #fb923c;
            box-shadow: 0 10px 16px rgba(251, 146, 60, 0.20);
        }

        /* Default target: 1024x600 — keep 3 columns, slightly compact, no forced 2-col */
        @media (max-width: 1024px) and (min-width: 901px) {
            .step1-body { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        @media (max-height: 650px) {
            .kiosk-title { font-size: 1.45rem; }
            .service-card { min-height: 180px; padding: 10px 10px 8px; }
            .service-icon { width: 46px; height: 46px; font-size: 22px; }
            .service-title { font-size: 18px; }
            .service-desc { font-size: 11px; min-height: 28px; }
            .service-cta { font-size: 16px; padding: 6px 8px; }
            .step1-head-title { font-size: 22px; }
            .step1-head-icon { width: 42px; height: 42px; font-size: 20px; }
            .step2-head-title { font-size: 24px; }
            .step2-body .text-2xl { font-size: 1.15rem !important; }
            .priority-btn .text-4xl { font-size: 1.7rem !important; }
            .priority-btn .text-lg { font-size: 0.9rem !important; }
            .step2-back-btn, .step2-print-btn { font-size: 18px; min-width: 140px; padding: 8px 12px; }
            .step3-service-name { font-size: 26px; }
            .step3-title { font-size: 20px; }
        }

        @media (max-width: 900px) {
            .step1-body { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .step1-head-icon { display: none; }
        }
        @media (max-width: 640px) {
            .step1-body { grid-template-columns: 1fr; }
            .step2-back-btn, .step2-print-btn,
            .step3-back-btn, .step3-confirm-btn {
                width: 100%;
                min-width: 0;
            }
            .step3-actions { flex-direction: column; }
            .step1-head-title { font-size: 22px; }
            .service-title { font-size: 20px; }
        }
    </style>
@endsection

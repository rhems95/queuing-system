@extends('layouts.app')

@section('title', 'Kiosk')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-6">
            <h1 class="text-4xl font-extrabold tracking-wide text-blue-950">KIOSK QUEUE TICKET SYSTEM</h1>
            <p id="stepIndicator" class="text-sm text-gray-600 mt-1 tracking-widest">STEP 1 OF 3 - SELECT SERVICE</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-red-700 text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="kioskForm" method="POST" action="{{ route('kiosk.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="service_id" id="serviceIdInput" value="{{ old('service_id') }}">
            <input type="hidden" name="output_mode" id="outputModeInput" value="">

            <div id="step1" class="step1-wrap mt-8">
                <div class="step-pill">STEP 1</div>
                <div class="step1-head">
                    <div class="step1-head-title">GET YOUR QUEUE NUMBER</div>
                    <div class="step1-head-sub">Please select the service you need</div>
                    <div class="step1-head-icon">🖥️</div>
                </div>
                <div class="step1-body">
                    @foreach($services as $service)
                        @php
                            $isCashier = str_contains(strtolower($service->service_name), 'cash');
                            $cardClass = $isCashier ? 'service-card cashier' : 'service-card registrar';
                            $iconClass = $isCashier ? 'service-icon cashier' : 'service-icon registrar';
                            $icon = $isCashier ? '💳' : '📝';
                            $desc = $isCashier ? 'Payment and cash related transactions' : 'Enrollment and records related services';
                        @endphp
                        <button
                            type="button"
                            class="service-btn {{ $cardClass }}"
                            data-service-id="{{ $service->id }}"
                        >
                            <div class="{{ $iconClass }}">{{ $icon }}</div>
                            <div class="service-title {{ $isCashier ? 'text-blue-950' : 'text-green-800' }}">{{ strtoupper($service->service_name) }}</div>
                            <div class="service-desc">{{ $desc }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div id="step2" class="hidden step2-wrap step2-registrar mt-8">
                <div class="step-pill step2-pill">STEP 2</div>
                <div id="step2Head" class="step2-head">
                    <div class="step2-head-icon">👤</div>
                    <div class="step2-head-copy">
                        <p class="step2-head-title">ENTER YOUR DETAILS</p>
                        <p class="step2-head-sub">Provide information for faster tracking</p>
                    </div>
                </div>
                <div class="step2-body">
                    <div class="step2-grid">
                        <div class="step2-left">
                            <label class="block text-3xl font-semibold mb-2 text-slate-800">Student Name <span class="text-gray-500 font-normal">(Optional)</span></label>
                            <input type="text" id="studentNameInput" name="student_name" value="{{ old('student_name') }}"
                                   placeholder="Enter student name"
                                   class="step2-input">

                            <label class="block text-3xl font-semibold mb-2 mt-5 text-slate-800">Student ID <span class="text-gray-500 font-normal">(Optional)</span></label>
                            <input type="text" name="student_id" value="{{ old('student_id') }}"
                                   placeholder="Enter student ID number"
                                   class="step2-input">
                        </div>

                        <div class="step2-right">
                            <label class="block text-3xl font-semibold mb-2 text-slate-900">Are you a priority customer?</label>
                            <div class="grid grid-cols-1 gap-3">
                                <button type="button" class="priority-btn priority-regular priority-selected border-2 rounded-xl px-5 py-4 font-semibold text-left" data-priority="regular">
                                    <div class="font-bold text-4xl leading-none">Regular</div>
                                </button>
                                <button type="button" class="priority-btn priority-priority border-2 rounded-xl px-5 py-4 font-semibold text-left" data-priority="priority">
                                    <div class="font-bold text-4xl leading-none">Priority</div>
                                    <div class="text-lg text-orange-700 font-normal mt-1">Senior, PWD, Pregnant, etc.</div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority', 'regular') }}">

                    <div class="flex justify-center gap-3 mt-6">
                        <button type="button" id="backToStep1" class="step2-back-btn" aria-label="Back">
                            <span class="step2-back-icon">←</span>
                        </button>
                        <button type="button" id="goToStep3" class="step2-continue text-white py-2 rounded-xl font-semibold px-10">Continue</button>
                    </div>
                </div>
            </div>

            <div id="step3" class="hidden step3-wrap mt-8">
                <div class="step-pill step3-pill">STEP 3</div>
                <div class="step3-head">
                    <div class="step3-head-icon">🎫</div>
                    <div>
                        <p class="step3-head-title">GET YOUR TICKET</p>
                        <p class="step3-head-sub">Choose how you want to receive your queue ticket</p>
                    </div>
                </div>
                <div class="step3-body">
                    <p class="step3-question">How would you like your ticket?</p>

                    <button type="button" id="printBtn" class="step3-option step3-option-print">
                        <div class="step3-option-left">
                            <div class="step3-option-icon step3-option-icon-print">🖨️</div>
                            <div>
                                <div class="step3-option-title step3-option-title-print">PRINT TICKET</div>
                                <div class="step3-option-desc">Get a physical ticket to wait for your number</div>
                            </div>
                        </div>
                        <div class="step3-arrow step3-arrow-print">›</div>
                    </button>

                    <button type="button" id="ecoBtn" class="step3-option step3-option-eco">
                        <div class="step3-option-left">
                            <div class="step3-option-icon step3-option-icon-eco">🌿</div>
                            <div>
                                <div class="step3-option-title step3-option-title-eco">SAVE PAPER (ECO MODE)</div>
                                <div class="step3-option-desc">View on screen only and take a photo</div>
                            </div>
                        </div>
                        <div class="step3-arrow step3-arrow-eco">›</div>
                    </button>
                    <p id="ecoDisabledHint" class="hidden text-center text-orange-700 font-semibold">
                        Enter a student name to enable Eco Mode.
                    </p>

                    <div class="step3-earth-strip">
                        <div class="step3-earth-title">Save Mother Earth!</div>
                        <div class="step3-earth-sub">Choose Eco Mode to help reduce paper consumption.</div>
                    </div>

                    <button type="button" id="backToStep2" class="w-full mt-3 bg-gray-200 text-gray-800 py-2 rounded-lg hover:bg-gray-300">
                        Back
                    </button>
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
            var outputModeInput = document.getElementById('outputModeInput');
            var kioskForm = document.getElementById('kioskForm');
            var selectedServiceTheme = 'registrar';
            var step2 = document.getElementById('step2');
            var studentNameInput = document.getElementById('studentNameInput');
            var ecoBtn = document.getElementById('ecoBtn');
            var ecoDisabledHint = document.getElementById('ecoDisabledHint');

            function showStep(stepNo) {
                step1.classList.add('hidden');
                step2.classList.add('hidden');
                step3.classList.add('hidden');

                if (stepNo === 1) {
                    step1.classList.remove('hidden');
                    stepIndicator.textContent = 'STEP 1 OF 3 - SELECT SERVICE';
                } else if (stepNo === 2) {
                    step2.classList.remove('hidden');
                    stepIndicator.textContent = 'STEP 2 OF 3 - ENTER DETAILS';
                } else {
                    step3.classList.remove('hidden');
                    stepIndicator.textContent = 'STEP 3 OF 3 - OUTPUT OPTION';
                }
            }

            document.querySelectorAll('.service-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    serviceIdInput.value = btn.dataset.serviceId;
                    selectedServiceTheme = btn.classList.contains('cashier') ? 'cashier' : 'registrar';
                    step2.classList.remove('step2-cashier', 'step2-registrar');
                    step2.classList.add(selectedServiceTheme === 'cashier' ? 'step2-cashier' : 'step2-registrar');
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

            document.getElementById('backToStep1').addEventListener('click', function () {
                showStep(1);
            });
            document.getElementById('goToStep3').addEventListener('click', function () {
                showStep(3);
            });
            document.getElementById('backToStep2').addEventListener('click', function () {
                showStep(2);
            });

            document.getElementById('printBtn').addEventListener('click', function () {
                outputModeInput.value = 'print';
                kioskForm.submit();
            });
            ecoBtn.addEventListener('click', function () {
                if (ecoBtn.disabled) return;
                outputModeInput.value = 'eco';
                kioskForm.submit();
            });

            function updateEcoAvailability() {
                var hasName = studentNameInput && studentNameInput.value.trim().length > 0;
                ecoBtn.disabled = !hasName;
                ecoBtn.classList.toggle('step3-option-disabled', !hasName);
                if (ecoDisabledHint) ecoDisabledHint.classList.toggle('hidden', hasName);
            }

            if (studentNameInput) {
                studentNameInput.addEventListener('input', updateEcoAvailability);
                updateEcoAvailability();
            }
        });
    </script>

    <style>
        .step1-wrap {
            position: relative;
            z-index: 20;
            background: #f4f7fb;
            border: 1px solid #d9dee7;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            overflow: visible;
        }
        .step-pill {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 40;
            background: linear-gradient(135deg, #2e7be6, #204fb7);
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.04em;
            border-radius: 999px;
            padding: 7px 34px;
            border: 2px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 6px 14px rgba(33, 93, 194, 0.35);
        }
        .step1-head {
            position: relative;
            z-index: 25;
            color: #fff;
            text-align: center;
            padding: 36px 20px 26px;
            background: linear-gradient(120deg, #1e88ea, #1f43ad);
            border-radius: 20px 20px 0 0;
        }
        .step1-head-title {
            font-size: 42px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 0.01em;
        }
        .step1-head-sub {
            margin-top: 6px;
            font-size: 18px;
            color: #dbeafe;
        }
        .step1-head-icon {
            position: absolute;
            right: 22px;
            top: 16px;
            width: 66px;
            height: 66px;
            border-radius: 999px;
            background: #ecf4ff;
            color: #1f4ca8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: inset 0 0 0 2px #d3e3ff;
        }
        .step1-body {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
            padding: 28px;
        }
        .service-card {
            background: #fff;
            border: 2px solid #d8e3f2;
            border-radius: 16px;
            padding: 26px 20px 22px;
            text-align: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 10px 14px rgba(15, 23, 42, 0.08);
        }
        .service-card:hover {
            transform: translateY(-2px);
        }
        .service-card.cashier {
            border-bottom: 8px solid #2196f3;
        }
        .service-card.registrar {
            border-color: #bce8d7;
            border-bottom: 8px solid #11a36a;
        }
        .service-icon {
            width: 100px;
            height: 100px;
            border-radius: 999px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 46px;
        }
        .service-icon.cashier {
            background: #d8ecff;
            color: #1f62c9;
        }
        .service-icon.registrar {
            background: #d9f5e7;
            color: #0f8b5f;
        }
        .service-title {
            font-size: 38px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: 0.02em;
        }
        .service-desc {
            margin-top: 8px;
            font-size: 26px;
            line-height: 1.25;
            color: #374151;
            font-weight: 500;
        }
        .step2-wrap {
            position: relative;
            border-radius: 20px;
            overflow: visible;
            border: 1px solid #c9dfcf;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            background: #eef8ec;
            z-index: 20;
        }
        .step2-pill {
            top: -18px;
        }
        .step2-head {
            border-radius: 20px 20px 0 0;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 18px;
            color: white;
        }
        .step2-head-icon {
            width: 70px;
            height: 70px;
            border-radius: 999px;
            background: #ecfff4;
            color: #13795b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.5);
        }
        .step2-head-title {
            font-size: 44px;
            font-weight: 800;
            line-height: 1.05;
        }
        .step2-head-sub {
            font-size: 18px;
            color: #ddffef;
            margin-top: 2px;
        }
        .step2-body {
            padding: 24px 26px;
            background: #f3fbef;
            border-radius: 0 0 20px 20px;
        }
        .step2-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }
        .step2-right {
            border-left: 2px solid rgba(100, 116, 139, 0.25);
            padding-left: 18px;
        }
        .step2-input {
            width: 100%;
            border: 2px solid #b9c6d7;
            border-radius: 12px;
            padding: 10px 14px;
            background: white;
            font-size: 24px;
        }
        .step2-back-btn {
            width: 64px;
            height: 54px;
            border-radius: 14px;
            border: 2px solid rgba(100, 116, 139, 0.35);
            background: rgba(255, 255, 255, 0.9);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.12s ease, background 0.12s ease;
        }
        .step2-back-btn:hover {
            background: #ffffff;
            transform: translateY(-1px);
        }
        .step2-back-icon {
            font-size: 28px;
            font-weight: 900;
            color: #334155;
            line-height: 1;
        }
        .step2-continue {
            background: linear-gradient(90deg, #1eab64, #0c7f4c);
            font-size: 30px;
            letter-spacing: 0.02em;
        }
        .step2-cashier {
            border-color: #b9d4ff;
            background: #edf4ff;
        }
        .step2-cashier .step2-head {
            background: linear-gradient(120deg, #2f7de6, #1f4db6);
        }
        .step2-cashier .step2-pill {
            background: linear-gradient(135deg, #2e7be6, #204fb7);
        }
        .step2-cashier .step2-head-sub {
            color: #deecff;
        }
        .step2-cashier .step2-body {
            background: #f1f6ff;
        }
        .step2-cashier .step2-continue {
            background: linear-gradient(90deg, #2f7de6, #1f4db6);
        }
        .step2-registrar .step2-head {
            background: linear-gradient(120deg, #169c63, #13784f);
        }
        .step2-registrar .step2-pill {
            background: linear-gradient(135deg, #169c63, #13784f);
        }
        .step2-registrar .step2-body {
            background: #f3fbef;
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
        .priority-btn:hover {
            transform: translateY(-1px);
        }
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
        .step3-wrap {
            position: relative;
            border-radius: 20px;
            overflow: visible;
            border: 1px solid #cdc8f3;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            background: #f6f3ff;
            z-index: 20;
        }
        .step3-pill {
            top: -18px;
            background: linear-gradient(135deg, #5e4fd8, #4437b2);
        }
        .step3-head {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 28px;
            border-radius: 20px 20px 0 0;
            background: linear-gradient(120deg, #6153d9, #3d2f9e);
            color: #fff;
        }
        .step3-head-icon {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            background: #ece9ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.55);
        }
        .step3-head-title {
            font-size: 44px;
            font-weight: 800;
            line-height: 1.05;
        }
        .step3-head-sub {
            font-size: 18px;
            color: #e6e0ff;
            margin-top: 2px;
        }
        .step3-body {
            padding: 24px 26px;
            border-radius: 0 0 20px 20px;
            background: #f8f5ff;
        }
        .step3-question {
            text-align: center;
            font-size: 40px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 14px;
        }
        .step3-option {
            width: 100%;
            border-radius: 16px;
            border: 3px solid transparent;
            background: #fff;
            padding: 14px 20px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            box-shadow: 0 8px 12px rgba(15, 23, 42, 0.08);
            position: relative;
            overflow: hidden;
        }
        .step3-option-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .step3-option-icon {
            width: 74px;
            height: 74px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
        }
        .step3-option-print {
            border-color: #2f7de6;
        }
        .step3-option-icon-print {
            background: #ddebff;
            color: #1e5bc0;
        }
        .step3-option-title-print {
            color: #1f4fb7;
        }
        .step3-option-eco {
            border-color: #17915d;
            background: #f0f9ee;
        }
        .step3-option-disabled {
            cursor: not-allowed;
            filter: grayscale(0.2);
        }
        .step3-option-disabled::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(1px);
            -webkit-backdrop-filter: blur(1px);
        }
        .step3-option-icon-eco {
            background: #d6f3cf;
            color: #11784f;
        }
        .step3-option-title-eco {
            color: #176647;
        }
        .step3-option-title {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.05;
        }
        .step3-option-desc {
            margin-top: 4px;
            font-size: 28px;
            color: #334155;
            font-weight: 500;
        }
        .step3-arrow {
            font-size: 62px;
            font-weight: 700;
            line-height: 1;
        }
        .step3-arrow-print {
            color: #2f7de6;
        }
        .step3-arrow-eco {
            color: #17915d;
        }
        .step3-earth-strip {
            margin-top: 8px;
            border-radius: 12px;
            background: #dff0ff;
            border: 1px solid #c5def4;
            padding: 10px 14px;
            text-align: center;
        }
        .step3-earth-title {
            color: #0f7a3e;
            font-size: 32px;
            font-weight: 800;
            line-height: 1.1;
        }
        .step3-earth-sub {
            color: #334155;
            font-size: 24px;
            font-weight: 500;
            margin-top: 2px;
        }
        @media (max-width: 1024px) {
            .step1-head-title { font-size: 34px; }
            .service-title { font-size: 30px; }
            .service-desc { font-size: 20px; }
            .step2-head-title { font-size: 32px; }
            .step2-input { font-size: 18px; }
            .step2-continue { font-size: 24px; }
            .step3-head-title { font-size: 32px; }
            .step3-question { font-size: 30px; }
            .step3-option-title { font-size: 28px; }
            .step3-option-desc { font-size: 20px; }
            .step3-earth-title { font-size: 24px; }
            .step3-earth-sub { font-size: 16px; }
        }
        @media (max-width: 768px) {
            .step1-body { grid-template-columns: 1fr; }
            .step1-head-icon { display: none; }
            .step1-head-title { font-size: 28px; }
            .service-title { font-size: 26px; }
            .service-desc { font-size: 18px; }
            .step2-grid { grid-template-columns: 1fr; }
            .step2-right { border-left: 0; padding-left: 0; }
            .step2-head-title { font-size: 26px; }
            .step2-input { font-size: 16px; }
            .step2-continue { font-size: 20px; }
            .step3-head { gap: 10px; }
            .step3-head-icon { width: 54px; height: 54px; font-size: 24px; }
            .step3-head-title { font-size: 24px; }
            .step3-head-sub { font-size: 14px; }
            .step3-question { font-size: 22px; }
            .step3-option-icon { width: 52px; height: 52px; font-size: 24px; }
            .step3-option-title { font-size: 20px; }
            .step3-option-desc { font-size: 14px; }
            .step3-arrow { font-size: 34px; }
            .step3-earth-title { font-size: 18px; }
            .step3-earth-sub { font-size: 13px; }
        }
    </style>
@endsection


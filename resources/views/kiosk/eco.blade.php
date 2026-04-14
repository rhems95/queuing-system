@extends('layouts.app')

@section('title', 'Eco Ticket')

@section('content')
    <div class="max-w-md mx-auto eco-wrap">
        <div class="step-pill step4b-pill">STEP 4B</div>
        <div class="eco-card">
            <div class="eco-head">ECO MODE - SAVE PAPER</div>
            <div class="eco-body">
                <div class="eco-leaf">🌿</div>
                <div class="eco-q-label">YOUR QUEUE NUMBER</div>
                <div class="eco-q-no">{{ $queue->queue_number }}</div>
                <div class="eco-divider"></div>

                <div class="eco-detail-row">
                    <span class="eco-detail-label">Service:</span>
                    <span class="eco-pill-blue">{{ strtoupper($service->service_name) }}</span>
                </div>
                <div class="eco-detail-row">
                    <span class="eco-detail-label">Priority:</span>
                    <span class="eco-pill-light">{{ strtoupper($priorityLabel) }}</span>
                </div>
                <div class="eco-detail-row">
                    <span class="eco-detail-label">Time:</span>
                    <span id="issuedAtText">{{ $issuedAt->format('M d, Y h:i A') }}</span>
                </div>

                <div class="eco-photo-box">
                    <span class="eco-photo-icon">📷</span>
                    <span>Please take a photo<br>of this screen.</span>
                </div>
            </div>
            <div class="eco-footer">
                Returning to home in <span id="countdown">20</span> seconds...
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('kiosk') }}" class="inline-block bg-gray-200 text-gray-800 px-5 py-2 rounded-lg hover:bg-gray-300 font-semibold">
                Home
            </a>
        </div>
    </div>

    <style>
        .eco-wrap {
            position: relative;
        }
        .step-pill {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-weight: 800;
            border-radius: 999px;
            padding: 7px 28px;
            border: 2px solid rgba(255, 255, 255, 0.6);
            letter-spacing: 0.03em;
            z-index: 3;
        }
        .step4b-pill {
            background: linear-gradient(135deg, #159c68, #0f7d53);
        }
        .eco-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #b7e0c8;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }
        .eco-head {
            background: linear-gradient(90deg, #0f8a5d, #0d6d49);
            color: #fff;
            font-weight: 800;
            text-align: center;
            padding: 18px 10px 14px;
            font-size: 34px;
        }
        .eco-body {
            background: #edfbe9;
            padding: 20px 22px;
            text-align: center;
            color: #104e3b;
        }
        .eco-leaf {
            width: 90px;
            height: 90px;
            border-radius: 999px;
            background: #d2f2c8;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
        }
        .eco-q-label {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .eco-q-no {
            font-size: 88px;
            font-weight: 900;
            line-height: 1;
            margin-top: 2px;
            margin-bottom: 8px;
        }
        .eco-divider {
            border-top: 2px solid #c3dcc9;
            margin-bottom: 10px;
        }
        .eco-detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 28px;
            margin-bottom: 6px;
            text-align: left;
        }
        .eco-detail-label {
            font-weight: 700;
            color: #23453a;
        }
        .eco-pill-blue {
            background: #245ac9;
            color: #fff;
            border-radius: 999px;
            padding: 4px 16px;
            font-weight: 800;
            font-size: 22px;
        }
        .eco-pill-light {
            background: #d9e9ff;
            color: #234b8f;
            border-radius: 999px;
            padding: 4px 16px;
            font-weight: 800;
            font-size: 22px;
        }
        .eco-photo-box {
            margin-top: 10px;
            border: 1px solid #b5d7b9;
            border-radius: 12px;
            background: #f4fff2;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 31px;
            font-weight: 700;
            line-height: 1.1;
            color: #1e4537;
        }
        .eco-photo-icon {
            font-size: 36px;
        }
        .eco-footer {
            background: #0d6d49;
            color: #e6fff5;
            text-align: center;
            padding: 10px 8px;
            font-size: 25px;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .eco-head { font-size: 24px; }
            .eco-q-label { font-size: 18px; }
            .eco-q-no { font-size: 58px; }
            .eco-detail-row { font-size: 18px; }
            .eco-pill-blue, .eco-pill-light { font-size: 14px; }
            .eco-photo-box { font-size: 20px; }
            .eco-footer { font-size: 16px; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var issuedEl = document.getElementById('issuedAtText');
            var issuedAt = new Date('{{ $issuedAt->toIso8601String() }}');
            if (issuedEl && !isNaN(issuedAt.getTime())) {
                issuedEl.textContent = issuedAt.toLocaleString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            var remaining = 20;
            var el = document.getElementById('countdown');
            var timer = setInterval(function () {
                remaining -= 1;
                if (el) el.textContent = String(remaining);
                if (remaining <= 0) {
                    clearInterval(timer);
                    window.location.href = '{{ route('kiosk') }}';
                }
            }, 1000);
        });
    </script>
@endsection


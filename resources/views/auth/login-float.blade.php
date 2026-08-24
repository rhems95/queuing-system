<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PECIT Staff Float</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #00005c;
            color: #fff;
            font-family: "Source Sans 3 Variable", "Segoe UI", Tahoma, Arial, sans-serif;
            user-select: none;
        }
        .fl {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            min-height: 220px;
            padding: 6px 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            background: linear-gradient(160deg, #00005c 0%, #000080 55%, #1a1a99 100%);
        }
        .fl-head {
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 2px solid #c9a227;
            padding-bottom: 4px;
        }
        .fl-head img {
            width: 22px;
            height: 22px;
            object-fit: contain;
            background: #fff;
            border-radius: 999px;
            padding: 1px;
            flex-shrink: 0;
        }
        .fl-head h1 {
            margin: 0;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.02em;
            line-height: 1.15;
        }
        .fl-head p {
            margin: 1px 0 0;
            font-size: 8px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .fl-error {
            background: rgba(185, 28, 28, 0.25);
            border: 1px solid rgba(254, 202, 202, 0.5);
            border-radius: 5px;
            padding: 3px 5px;
            font-size: 9px;
            line-height: 1.25;
        }
        .fl-error ul {
            margin: 0;
            padding-left: 14px;
        }
        .fl form {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin: 0;
        }
        .fl label {
            display: block;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            opacity: 0.85;
            margin-bottom: 1px;
        }
        .fl input {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-size: 11px;
            padding: 5px 7px;
            outline: none;
        }
        .fl input::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }
        .fl input:focus {
            border-color: #c9a227;
            background: rgba(255, 255, 255, 0.18);
        }
        .fl-btn {
            width: 100%;
            margin-top: 2px;
            border: 0;
            border-radius: 5px;
            background: #c9a227;
            color: #1a1400;
            font-size: 11px;
            font-weight: 800;
            padding: 6px 8px;
            cursor: pointer;
        }
        .fl-btn:active {
            transform: translateY(1px);
        }
    </style>
</head>
<body>
    <div class="fl">
        <div class="fl-head">
            <img src="{{ asset('logo/logo.png') }}" alt="PECIT">
            <div>
                <h1>Staff Float Login</h1>
                <p>Sign in to continue</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="fl-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <input type="hidden" name="float_login" value="1">

            <div>
                <label for="floatEmail">Email</label>
                <input
                    id="floatEmail"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="Email"
                    autocomplete="username"
                >
            </div>

            <div>
                <label for="floatPassword">Password</label>
                <input
                    id="floatPassword"
                    type="password"
                    name="password"
                    required
                    placeholder="Password"
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="fl-btn">Login</button>
        </form>
    </div>
</body>
</html>

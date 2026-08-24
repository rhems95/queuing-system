<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $about['title'] ?? 'About' }} · PECIT Capstone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .about-page {
            --about-navy: #000080;
            --about-navy-deep: #00005c;
            --about-gold: #c9a227;
            --about-surface: #f4f6fb;
            min-height: 100vh;
            background:
                radial-gradient(ellipse 70% 45% at 100% 0%, rgba(0, 0, 128, 0.10), transparent 55%),
                radial-gradient(ellipse 55% 40% at 0% 100%, rgba(201, 162, 39, 0.10), transparent 50%),
                var(--about-surface);
            color: #1e2433;
            font-family: "Source Sans 3 Variable", "Segoe UI", Tahoma, Arial, sans-serif;
        }
        .about-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }
        .about-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .about-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .about-brand img {
            width: 3rem;
            height: 3rem;
            object-fit: contain;
            border-radius: 999px;
            background: #fff;
            border: 2px solid var(--about-gold);
            padding: 0.2rem;
        }
        .about-brand h1 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--about-navy-deep);
            letter-spacing: -0.01em;
        }
        .about-brand p {
            margin: 0.15rem 0 0;
            font-size: 0.8rem;
            color: #5b6478;
        }
        .about-close {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #d5d9e8;
            background: #fff;
            color: var(--about-navy);
            border-radius: 0.55rem;
            padding: 0.55rem 0.9rem;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            cursor: pointer;
        }
        .about-close:hover { background: #e8eaf6; }
        .about-hero {
            background: linear-gradient(120deg, var(--about-navy-deep), var(--about-navy) 60%, #1a1a99);
            color: #fff;
            border-radius: 1rem;
            padding: 1.5rem 1.4rem;
            border-bottom: 3px solid var(--about-gold);
            box-shadow: 0 12px 28px rgba(0, 0, 80, 0.18);
            margin-bottom: 1.5rem;
        }
        .about-hero .eyebrow {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--about-gold);
            margin-bottom: 0.45rem;
        }
        .about-hero h2 {
            margin: 0 0 0.45rem;
            font-size: clamp(1.4rem, 3vw, 1.9rem);
            font-weight: 800;
        }
        .about-hero .org {
            margin: 0 0 0.75rem;
            opacity: 0.9;
            font-size: 0.92rem;
        }
        .about-hero .desc {
            margin: 0;
            max-width: 48rem;
            line-height: 1.5;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.92);
        }
        .about-section-title {
            margin: 0 0 0.85rem;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--about-navy);
        }
        .about-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1fr;
        }
        @media (min-width: 640px) {
            .about-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 900px) {
            .about-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @media (min-width: 1100px) {
            .about-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }
        .about-card {
            background: #fff;
            border: 1px solid #d5d9e8;
            border-radius: 0.95rem;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 80, 0.06);
            display: flex;
            flex-direction: column;
        }
        .about-photo {
            aspect-ratio: 1 / 1;
            background: linear-gradient(160deg, #e8eaf6, #dfe3f5);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .about-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .about-photo .initials {
            width: 4.5rem;
            height: 4.5rem;
            border-radius: 999px;
            background: var(--about-navy);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: 0.04em;
            border: 3px solid var(--about-gold);
        }
        .about-card-body {
            padding: 0.95rem 0.95rem 1.1rem;
            text-align: center;
        }
        .about-card-body h3 {
            margin: 0 0 0.25rem;
            font-size: 1rem;
            font-weight: 800;
            color: var(--about-navy-deep);
            line-height: 1.25;
        }
        .about-role {
            margin: 0 0 0.45rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #8a6d12;
            background: #f7f1de;
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
        }
        .about-focus {
            margin: 0.45rem 0 0;
            font-size: 0.78rem;
            color: #5b6478;
            line-height: 1.4;
        }
        .about-foot {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.78rem;
            color: #5b6478;
        }
        .about-foot kbd {
            font-family: inherit;
            font-weight: 700;
            background: #fff;
            border: 1px solid #d5d9e8;
            border-radius: 0.35rem;
            padding: 0.1rem 0.4rem;
        }
    </style>
</head>
<body class="about-page">
    <div class="about-wrap">
        <div class="about-top">
            <div class="about-brand">
                <img src="{{ asset('logo/logo.png') }}" alt="PECIT Logo">
                <div>
                    <h1>{{ $about['project'] ?? 'PECIT Queuing System' }}</h1>
                    <p>{{ $about['subtitle'] ?? 'Capstone Research Project' }}</p>
                </div>
            </div>
            <a class="about-close" href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}">← Close</a>
        </div>

        <section class="about-hero">
            <div class="eyebrow">Secret Capstone Credits</div>
            <h2>{{ $about['title'] ?? 'About This Capstone' }}</h2>
            <p class="org">{{ $about['institution'] ?? '' }}</p>
            <p class="desc">{{ $about['description'] ?? '' }}</p>
        </section>

        <h2 class="about-section-title">Research Group</h2>
        <div class="about-grid">
            @foreach ($members as $member)
                <article class="about-card">
                    <div class="about-photo">
                        @if (!empty($member['photo_url']))
                            <img src="{{ $member['photo_url'] }}" alt="{{ $member['name'] }}">
                        @else
                            <div class="initials">{{ $member['initials'] }}</div>
                        @endif
                    </div>
                    <div class="about-card-body">
                        <h3>{{ $member['name'] }}</h3>
                        <div class="about-role">{{ $member['role'] }}</div>
                        @if (!empty($member['focus']))
                            <p class="about-focus">{{ $member['focus'] }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <p class="about-foot">
            Open anytime with <kbd>{{ $about['shortcut_hint'] ?? 'Ctrl + Alt + Shift + A' }}</kbd>
            · Not listed in menus
        </p>
    </div>

    @include('partials.secret-about-hotkey')
</body>
</html>

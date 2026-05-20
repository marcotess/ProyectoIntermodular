<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Gestion de Cursos')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/js/app.js'])
    @endif
    <style>
        :root {
            --paper: #fcfaf7;
            --paper-strong: #fffdfb;
            --ink: #20171a;
            --muted: #6f5d65;
            --line: rgba(83, 34, 44, 0.12);
            --accent: #7c2d3c;
            --accent-soft: #efe0e4;
            --accent-strong: #5f2230;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(124, 45, 60, 0.14), transparent 32%),
                radial-gradient(circle at bottom right, rgba(124, 45, 60, 0.10), transparent 28%),
                linear-gradient(180deg, #fffefd 0%, var(--paper) 100%);
        }

        .brand-mark {
            font-family: 'Fraunces', serif;
            letter-spacing: 0.18em;
            transition: transform 180ms ease, opacity 180ms ease;
        }

        .brand-mark:hover {
            transform: scale(1.04);
        }

        .guest-surface {
            background: rgba(255, 253, 251, 0.88);
            border: 1px solid var(--line);
            box-shadow: 0 26px 70px rgba(74, 28, 39, 0.10);
            backdrop-filter: blur(18px);
        }

        .field-shell {
            border: 1px solid rgba(83, 34, 44, 0.12);
            background: rgba(255,255,255,0.78);
            transition: border-color 180ms ease, box-shadow 180ms ease, background 180ms ease;
        }

        .field-shell:focus {
            border-color: rgba(124, 45, 60, 0.45);
            box-shadow: 0 0 0 4px rgba(124, 45, 60, 0.10);
            background: #fff;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
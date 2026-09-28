<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $schoolProfile->nama_sistem ?? 'Sistem Inventaris SMPN 3 Babakancikao')</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Tailwind CSS CDN Fallback & Build -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#0F766E',
                        'primary-hover': '#115E59',
                        'primary-tint': '#CCFBF1',
                        'primary-soft': '#F0FDFA',
                        'sidebar-bg': '#134E4A',
                        'sidebar-text': '#CCFBF1',
                        'accent': '#F59E0B',
                        'accent-tint': '#FEF3C7',
                        'surface': '#FFFFFF',
                        'border-light': '#D9E4E2',
                        'border-strong': '#B7C9C6',
                        'text-main': '#1F2937',
                        'text-secondary': '#4B5563',
                        'text-muted': '#6B7280',
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --color-primary: #0F766E;
            --color-primary-hover: #115E59;
            --color-primary-tint: #CCFBF1;
            --color-primary-soft: #F0FDFA;
            --color-sidebar: #134E4A;
            --color-sidebar-text: #CCFBF1;
            --color-accent: #F59E0B;
            --color-accent-tint: #FEF3C7;
            --color-bg: #F3F7F6;
            --color-surface: #FFFFFF;
            --color-border: #D9E4E2;
            --color-border-strong: #B7C9C6;
            --color-text: #1F2937;
            --color-text-secondary: #4B5563;
            --color-text-muted: #6B7280;
            --font-sans: "Inter", system-ui, -apple-system, sans-serif;
            --focus-ring: 0 0 0 3px rgba(15,118,110,0.35);
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--color-bg);
            color: var(--color-text);
            margin: 0;
            padding: 0;
        }
    </style>

    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="antialiased min-h-screen flex flex-col justify-between">
    @yield('content')
</body>
</html>

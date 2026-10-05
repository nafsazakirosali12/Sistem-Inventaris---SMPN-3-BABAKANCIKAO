<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Peminjaman Inventaris - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                        'accent': '#F59E0B',
                        'surface': '#FFFFFF',
                        'border-light': '#D9E4E2',
                        'text-main': '#1F2937',
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
            --color-bg: #F3F7F6;
            --font-sans: "Inter", system-ui, -apple-system, sans-serif;
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

    @stack('styles')
</head>
<body class="antialiased bg-[#F3F7F6] text-[#1F2937] min-h-screen flex flex-col justify-between">

    <!-- Peminjam Navbar (Tanpa Sidebar) -->
    @include('components.peminjam.navbar')

    <!-- Main Workspace Content -->
    <main class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6">
        @yield('content')
    </main>

    <!-- Dynamic Shared School Footer -->
    @include('components.shared.footer')

    <!-- SweetAlert Toast Notifications for Flash Session Messages -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                }
            @endif

            @if (session('error'))
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Perhatian',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#0F766E',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold'
                        }
                    });
                }
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>

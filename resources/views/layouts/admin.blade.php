<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Sarpras - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}">
    <link rel="shortcut icon" href="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}">

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
                        'header-bg': '#134E4A',
                        'accent': '#F59E0B',
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
            --color-header: #134E4A;
            --color-bg: #F3F7F6;
            --color-border: #D9E4E2;
            --font-sans: "Inter", system-ui, -apple-system, sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--color-bg);
            color: #1F2937;
            margin: 0;
            padding: 0;
        }
    </style>

    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')
</head>
<body class="antialiased bg-[#F3F7F6] text-[#1F2937] min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Admin Top Navbar -->
    @include('components.admin.navbar')

    <!-- Mobile Overlay Backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-30 bg-black/50 backdrop-blur-xs md:hidden"
         style="display: none;"></div>

    <!-- Admin Left Sidebar (White Modern Light Theme) -->
    @include('components.admin.sidebar')

    <!-- Main Workspace Wrapper -->
    <div class="md:pl-64 flex flex-col min-h-screen pt-16 transition-all duration-200">
        <main class="relative flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 bg-[#F3F7F6]">
            @yield('content')
        </main>

        <!-- Dynamic Shared School Footer (White Theme & Copyright Only for Admin) -->
        @include('components.shared.footer', ['theme' => 'light', 'onlyCopyright' => true])
    </div>

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
                            popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2]',
                            confirmButton: 'px-5 py-2.5 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                        }
                    });
                }
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>

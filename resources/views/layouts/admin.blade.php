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

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

        /* ── Responsive Table Utility ─────────────────────── */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive table {
            min-width: 600px;
        }

        /* ── Mobile-Friendly Touch Targets ───────────────── */
        @media (max-width: 767px) {
            .page-header-actions {
                width: 100%;
            }
            .page-header-actions a,
            .page-header-actions button {
                width: 100%;
                justify-content: center;
            }
            /* Ensure modal contents don't overflow viewport */
            .swal2-popup {
                max-width: calc(100vw - 2rem) !important;
            }
        }

        /* ── Sidebar Transition ───────────────────────────── */
        aside {
            transition: transform 0.2s ease-in-out;
        }

        /* ── Prevent body scroll when sidebar is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }

        /* ── Custom Select2 (Admin Modern Theme) ──────────── */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            background-color: #F3F7F6;
            border: 1px solid #D9E4E2;
            border-radius: 0.5rem; /* rounded-lg (8px) */
            height: 40px; /* h-10 */
            display: flex;
            align-items: center;
            transition: all 0.15s ease-in-out;
            position: relative;
        }
        .select2-container--default .select2-selection--single:hover {
            border-color: #0F766E;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default .select2-selection--single:focus {
            background-color: #ffffff;
            border-color: #0F766E;
            box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.2);
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1F2937;
            font-size: 0.75rem; /* 12px (text-xs) */
            font-family: inherit;
            font-weight: 500;
            line-height: 38px;
            padding-left: 0.75rem;
            padding-right: 2rem;
            display: block;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6B7280;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
            width: 20px;
            position: absolute;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6B7280 transparent transparent transparent;
            border-style: solid;
            border-width: 5px 4px 0 4px;
            height: 0;
            left: 50%;
            margin-left: -4px;
            margin-top: -2px;
            position: absolute;
            top: 50%;
            width: 0;
            transition: transform 0.2s ease;
        }
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #0F766E transparent;
            border-width: 0 4px 5px 4px;
        }
        .select2-dropdown {
            border: 1px solid #D9E4E2;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            overflow: hidden;
            z-index: 99999;
            font-size: 0.75rem;
            margin-top: 4px;
        }
        .select2-container--default .select2-search--dropdown {
            padding: 8px;
            background-color: #F8FBFA;
            border-bottom: 1px solid #D9E4E2;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #D9E4E2;
            border-radius: 0.375rem;
            padding: 6px 10px;
            font-size: 0.75rem;
            outline: none;
            background-color: #ffffff;
            width: 100%;
            box-sizing: border-box;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #0F766E;
            box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.15);
        }
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            font-size: 0.75rem;
            color: #1F2937;
            user-select: none;
            transition: background-color 0.1s;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0F766E;
            color: #ffffff;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #F0FDFA;
            color: #0F766E;
            font-weight: 600;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected=true] {
            background-color: #0F766E;
            color: #ffffff;
        }
    </style>

    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')
</head>
<body class="antialiased bg-[#F3F7F6] text-[#1F2937] min-h-screen" x-data="{ sidebarOpen: false }" x-effect="document.body.classList.toggle('sidebar-open', sidebarOpen)">

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

    <!-- Global Select2 Filter & Form Initializer with Search Feature -->
    <script>
        $(document).ready(function() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2-filter').each(function() {
                    const $select = $(this);
                    $select.select2({
                        width: '100%',
                        dropdownAutoWidth: false,
                        minimumResultsForSearch: 0 // Pastikan fitur pencarian (search) selalu aktif
                    }).on('select2:select', function(e) {
                        if ($select.data('auto-submit') !== false) {
                            $select.closest('form').submit();
                        }
                    });
                });

                $('.select2-form').each(function() {
                    const $select = $(this);
                    $select.select2({
                        width: '100%',
                        dropdownAutoWidth: false,
                        minimumResultsForSearch: 0 // Pastikan fitur pencarian selalu aktif agar bisa ngetik
                    });
                });

                // Auto-focus input pencarian saat dropdown Select2 dibuka
                $(document).on('select2:open', function() {
                    setTimeout(function() {
                        const searchField = document.querySelector('.select2-container--open .select2-search__field');
                        if (searchField) {
                            searchField.focus();
                            if (!searchField.getAttribute('placeholder')) {
                                searchField.setAttribute('placeholder', 'Ketik untuk mencari...');
                            }
                        }
                    }, 50);
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

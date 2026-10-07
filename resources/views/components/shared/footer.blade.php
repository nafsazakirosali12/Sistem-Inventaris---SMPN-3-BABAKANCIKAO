@php
    $isLight = isset($theme) && $theme === 'light';
    $onlyCopyright = $onlyCopyright ?? false;
@endphp

<footer class="w-full {{ $onlyCopyright ? 'py-4 sm:py-5' : 'py-8 sm:py-10' }} border-t mt-auto transition-colors duration-200 {{ $isLight ? 'bg-white text-[#1F2937] border-[#E5E9E8] shadow-xs' : 'bg-[#134E4A] text-[#CCFBF1] border-[#115E59]' }}">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(! $onlyCopyright)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 items-start">

                {{-- 1. Identitas & Profil Sekolah --}}
                <div class="flex flex-col items-start text-left gap-3">
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ (!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo))) ? asset($schoolProfile->logo) : asset('images/logo-sekolah.png') }}"
                            alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}"
                            class="h-10 w-10 object-contain shrink-0"
                        >

                        <div class="text-left">
                            <h3 class="text-xs font-bold uppercase tracking-wider leading-snug {{ $isLight ? 'text-[#1F2937]' : 'text-white' }}">
                                {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                            </h3>
                            <p class="text-[11px] {{ $isLight ? 'text-[#6B7280]' : 'text-[#CCFBF1]/80' }}">
                                NPSN: {{ $schoolProfile->npsn ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <p class="text-xs leading-relaxed text-left {{ $isLight ? 'text-[#4B5563]' : 'text-[#CCFBF1]/90' }}">
                        {{ $schoolProfile->nama_sistem ?? 'Sistem Informasi Inventaris & Peminjaman' }}
                    </p>
                </div>

                {{-- 2. Kontak Resmi --}}
                <div class="flex flex-col items-start text-left gap-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-left {{ $isLight ? 'text-[#1F2937]' : 'text-white' }}">
                        Kontak Resmi
                    </h4>

                    <div class="flex flex-col items-start gap-2.5 w-full">
                        @if(isset($schoolProfile->alamat) && $schoolProfile->alamat)
                            <a
                                href="https://www.google.com/maps/search/?api=1&query={{ urlencode($schoolProfile->alamat) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-start text-left gap-2.5 text-xs transition-colors w-full {{ $isLight ? 'text-[#4B5563] hover:text-[#0F766E]' : 'text-[#CCFBF1]/90 hover:text-white' }}"
                                title="Buka lokasi di Google Maps"
                            >
                                <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5 group-hover:scale-110 transition-transform {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    location_on
                                </span>
                                <span class="leading-relaxed group-hover:underline">
                                    {{ $schoolProfile->alamat }}
                                </span>
                            </a>
                        @else
                            <div class="flex items-center text-left gap-2.5 text-xs w-full {{ $isLight ? 'text-[#9CA3AF]' : 'text-[#CCFBF1]/60' }}">
                                <span class="material-symbols-outlined text-[18px] shrink-0 {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    location_on
                                </span>
                                <span>Alamat belum diisi.</span>
                            </div>
                        @endif

                        @if(isset($schoolProfile->telepon) && $schoolProfile->telepon)
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $schoolProfile->telepon);
                                if (Str::startsWith($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                                $waUrl = 'https://wa.me/' . $cleanPhone;
                            @endphp
                            <a
                                href="{{ $waUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center text-left gap-2.5 text-xs transition-colors w-full {{ $isLight ? 'text-[#4B5563] hover:text-[#0F766E]' : 'text-[#CCFBF1]/90 hover:text-white' }}"
                                title="Hubungi WhatsApp {{ $schoolProfile->telepon }}"
                            >
                                <span class="material-symbols-outlined text-[18px] shrink-0 {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    call
                                </span>
                                <span class="group-hover:underline">{{ $schoolProfile->telepon }}</span>
                            </a>
                        @endif

                        @if(isset($schoolProfile->email) && $schoolProfile->email)
                            <a
                                href="mailto:{{ $schoolProfile->email }}"
                                class="group flex items-center text-left gap-2.5 text-xs transition-colors w-full {{ $isLight ? 'text-[#4B5563] hover:text-[#0F766E]' : 'text-[#CCFBF1]/90 hover:text-white' }}"
                                title="Kirim email"
                            >
                                <span class="material-symbols-outlined text-[18px] shrink-0 {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    mail
                                </span>
                                <span class="group-hover:underline">{{ $schoolProfile->email }}</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 3. Media Sosial Resmi --}}
                <div class="flex flex-col items-start text-left gap-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-left {{ $isLight ? 'text-[#1F2937]' : 'text-white' }}">
                        Media Sosial Resmi
                    </h4>

                    <div class="flex flex-col items-start gap-2.5 w-full">
                        @if(isset($schoolProfile->instagram) && $schoolProfile->instagram)
                            @php
                                $igText = Str::startsWith($schoolProfile->instagram, 'http') ? 'Instagram' : $schoolProfile->instagram;
                            @endphp
                            <a
                                href="{{ Str::startsWith($schoolProfile->instagram, 'http') ? $schoolProfile->instagram : 'https://instagram.com/' . ltrim($schoolProfile->instagram, '@') }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center text-left gap-2.5 text-xs transition-colors w-full {{ $isLight ? 'text-[#4B5563] hover:text-[#0F766E]' : 'text-[#CCFBF1]/90 hover:text-white' }}"
                                title="Buka Instagram {{ $igText }}"
                            >
                                <span class="material-symbols-outlined text-[18px] shrink-0 {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    photo_camera
                                </span>
                                <span class="group-hover:underline">{{ $igText }}</span>
                            </a>
                        @endif

                        @if(isset($schoolProfile->youtube) && $schoolProfile->youtube)
                            @php
                                $ytText = Str::startsWith($schoolProfile->youtube, 'http') ? 'YouTube' : $schoolProfile->youtube;
                            @endphp
                            <a
                                href="{{ Str::startsWith($schoolProfile->youtube, 'http') ? $schoolProfile->youtube : (Str::startsWith($schoolProfile->youtube, '@') ? 'https://youtube.com/' . $schoolProfile->youtube : 'https://youtube.com/@' . ltrim($schoolProfile->youtube, '@')) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center text-left gap-2.5 text-xs transition-colors w-full {{ $isLight ? 'text-[#4B5563] hover:text-[#0F766E]' : 'text-[#CCFBF1]/90 hover:text-white' }}"
                                title="Buka YouTube {{ $ytText }}"
                            >
                                <span class="material-symbols-outlined text-[18px] shrink-0 {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    smart_display
                                </span>
                                <span class="group-hover:underline">{{ $ytText }}</span>
                            </a>
                        @endif

                        @if(empty($schoolProfile->instagram) && empty($schoolProfile->youtube))
                            <div class="flex items-center text-left gap-2.5 text-xs w-full {{ $isLight ? 'text-[#9CA3AF]' : 'text-[#CCFBF1]/60' }}">
                                <span class="material-symbols-outlined text-[18px] shrink-0 {{ $isLight ? 'text-[#0F766E]' : 'text-[#2DD4BF]' }}">
                                    share
                                </span>
                                <span>Belum ada media sosial.</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endif

        {{-- Copyright --}}
        <div class="{{ $onlyCopyright ? 'text-center' : 'border-t mt-8 pt-5 text-center' }} text-[11px] leading-relaxed {{ $isLight ? 'border-[#E5E9E8] text-[#6B7280]' : 'border-[#115E59]/50 text-[#CCFBF1]/70' }}">
            © {{ date('Y') }} {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }} • Seluruh hak cipta dilindungi.
        </div>

    </div>
</footer>
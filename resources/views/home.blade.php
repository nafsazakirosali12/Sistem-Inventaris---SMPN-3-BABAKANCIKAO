@extends('layouts.peminjam')

@section('title', 'Beranda - ' . ($schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao'))

@section('content')
<div class="w-full">

    <!-- ═══════════════════════════════════════════════════════════
         1. HERO SECTION – Full-width dengan foto background sekolah
    ════════════════════════════════════════════════════════════ -->
    <section class="relative w-full overflow-hidden bg-[#F3F7F6] min-h-[480px] sm:min-h-[520px] md:min-h-[560px] flex items-center">

        <!-- Background: foto sekolah dengan overlay gelap -->
        <div class="absolute inset-x-0 top-0 bottom-0 z-0 bg-[#0A3D36]">
            @if(!empty($schoolProfile->foto) && file_exists(public_path($schoolProfile->foto)))
                <img src="{{ asset($schoolProfile->foto) }}"
                     alt="Foto Gedung {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                     class="w-full h-full object-cover filter brightness-90 contrast-105"
                     style="object-position: center 38%;">
            @else
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3i-EqOWnfvzL0n1CmzuUAAZF-_r1vAecPzbtxoBzIVk1XKQ4XupWk3jIOZeM9iig9ABMTleFy3CVrkLsMA_vyGXJ78f4i9U9sucQR9wUYfbBZmDr016mZsQ3noRBZCKvCvkjp1oHuqALKdAOXTcreyMsSS-bwBlYUVJkRqX3_IFtESOsHikQCFF06qKreOPsdx0dHeObcrCTTovdzwybSKDIYvVfjmWtkaVE8gCEaNl6AuQPzE7hzpSgCTe7pfxu9Ruc"
                     alt="Background {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                     class="w-full h-full object-cover filter brightness-90 contrast-105"
                     style="object-position: center 38%;">
            @endif
            <div class="absolute inset-0 bg-[#003833]/65 mix-blend-multiply"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#00302b]/90 via-[#004840]/75 to-transparent"></div>
        </div>

        <!-- Konten Hero -->
        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 md:py-24">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-7 max-w-4xl">

                <!-- Logo Sekolah -->
                <div class="shrink-0 w-20 h-20 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-2xl flex items-center justify-center filter drop-shadow-lg">
                    @if(!empty($schoolProfile->logo) && file_exists(public_path($schoolProfile->logo)))
                        <img src="{{ asset($schoolProfile->logo) }}"
                             alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                             class="w-full h-full object-contain filter drop-shadow-md">
                    @elseif(file_exists(public_path('images/logo-sekolah.png')))
                        <img src="{{ asset('images/logo-sekolah.png') }}"
                             alt="Logo {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                             class="w-full h-full object-contain filter drop-shadow-md">
                    @else
                        <div class="w-full h-full rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                            <span class="material-symbols-outlined text-5xl">school</span>
                        </div>
                    @endif
                </div>

                <!-- Teks & Tombol -->
                <div class="flex flex-col items-center sm:items-start text-center sm:text-left text-white w-full">

                    <!-- Tagline Pill -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/90 mb-3">
                        <span class="material-symbols-outlined text-[14px] text-[#b5ede7]">account_balance</span>
                        <span class="text-[11px] sm:text-xs tracking-wider uppercase font-semibold text-[#CCFBF1]">{{ $schoolProfile->tagline ?? 'PORTAL RESMI SARANA & PRASARANA' }}</span>
                    </div>

                    <!-- Nama Sekolah -->
                    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-white leading-tight mb-2">
                        {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                    </h1>

                    <!-- Nama Sistem -->
                    <h2 class="text-base sm:text-lg md:text-xl font-semibold text-white/90 tracking-tight mb-3">
                        {{ $schoolProfile->nama_sistem ?? 'Sistem Inventaris & Peminjaman' }}
                    </h2>

                    <!-- Deskripsi -->
                    <p class="text-sm text-white/80 leading-relaxed max-w-xl mb-6">
                        Fasilitas peminjaman sarana pembelajaran untuk mendukung aktivitas belajar mengajar di sekolah.
                    </p>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 mb-5">
                        <a href="{{ route('admin.inventaris.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#0F766E] hover:bg-[#115E59] active:scale-[0.98] text-white text-sm font-semibold shadow-md transition-all border border-[#CCFBF1]/30 focus:outline-none focus:ring-2 focus:ring-white/30">
                            <span class="material-symbols-outlined text-[18px]">category</span>
                            <span>Lihat Katalog Barang</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                        <a href="{{ route('admin.peminjaman.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white/10 backdrop-blur-sm border border-white/30 text-white text-sm font-semibold hover:bg-white hover:text-[#0F766E] active:scale-[0.98] transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-white/30">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    </div>

                    <!-- Info Badge Baris -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/20 backdrop-blur-sm border border-white/10 text-white/85 text-[11px]">
                            <span class="material-symbols-outlined text-[13px] text-[#FEF3C7]">schedule</span>
                            07.00 – 15.30 WIB
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/20 backdrop-blur-sm border border-white/10 text-white/85 text-[11px]">
                            <span class="material-symbols-outlined text-[13px] text-[#b5ede7]">check_circle</span>
                            Pengembalian Tepat Waktu
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/20 backdrop-blur-sm border border-white/10 text-white/85 text-[11px]">
                            <span class="material-symbols-outlined text-[13px] text-[#CCFBF1]">verified_user</span>
                            Verifikasi Otomatis Admin
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave divider bawah – menyatu natural ke background halaman -->
        <div class="absolute -bottom-[2px] left-0 right-0 w-full overflow-hidden leading-none z-10 pointer-events-none"
             style="margin-bottom: -1px; border: none; outline: none;">
            <svg class="relative block w-full h-10 sm:h-14 translate-y-[2px]"
                 preserveAspectRatio="none" viewBox="0 0 1200 122"
                 xmlns="http://www.w3.org/2000/svg"
                 style="display: block; width: calc(100% + 2px); margin-left: -1px; border: none; outline: none;">
                <path d="M0,35 C200,90 420,-15 650,55 C880,120 1050,20 1200,45 L1200,135 L0,135 Z"
                      fill="#F3F7F6" stroke="none"></path>
            </svg>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════
         KONTEN UTAMA – Sections bawah hero
    ════════════════════════════════════════════════════════════ -->
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 md:py-12">
        <div class="flex flex-col gap-8 sm:gap-10">

            <!-- ─── 2. LANGKAH PEMINJAMAN ─────────────────────── -->
            <section class="w-full">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Step 1 -->
                    <div class="bg-white rounded-xl p-5 border border-[#D9E4E2] shadow-sm flex items-start gap-4 hover:-translate-y-0.5 transition-transform">
                        <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] text-[#0F766E] flex items-center justify-center shrink-0 border border-[#CCFBF1]">
                            <span class="material-symbols-outlined text-[22px]">search_check</span>
                        </div>
                        <div class="space-y-1 min-w-0">
                            <span class="text-[11px] font-semibold text-[#0F766E] uppercase tracking-wider block">Langkah 01</span>
                            <h3 class="text-sm font-semibold text-[#1F2937]">Cari &amp; Pilih Barang</h3>
                            <p class="text-xs text-[#6B7280] leading-relaxed">Jelajahi katalog dan periksa kepastian kuota stok unit siap pakai secara realtime.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white rounded-xl p-5 border border-[#D9E4E2] shadow-sm flex items-start gap-4 hover:-translate-y-0.5 transition-transform">
                        <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] text-[#0F766E] flex items-center justify-center shrink-0 border border-[#CCFBF1]">
                            <span class="material-symbols-outlined text-[22px]">edit_calendar</span>
                        </div>
                        <div class="space-y-1 min-w-0">
                            <span class="text-[11px] font-semibold text-[#0F766E] uppercase tracking-wider block">Langkah 02</span>
                            <h3 class="text-sm font-semibold text-[#1F2937]">Isi Formulir Pinjam</h3>
                            <p class="text-xs text-[#6B7280] leading-relaxed">Tentukan jumlah unit, peruntukan kelas/kegiatan, serta jadwal tanggal pengembalian.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white rounded-xl p-5 border border-[#D9E4E2] shadow-sm flex items-start gap-4 hover:-translate-y-0.5 transition-transform">
                        <div class="w-10 h-10 rounded-lg bg-[#F0FDFA] text-[#0F766E] flex items-center justify-center shrink-0 border border-[#CCFBF1]">
                            <span class="material-symbols-outlined text-[22px]">inventory</span>
                        </div>
                        <div class="space-y-1 min-w-0">
                            <span class="text-[11px] font-semibold text-[#0F766E] uppercase tracking-wider block">Langkah 03</span>
                            <h3 class="text-sm font-semibold text-[#1F2937]">Ambil di Gudang Sarpras</h3>
                            <p class="text-xs text-[#6B7280] leading-relaxed">Tunjukkan bukti permohonan disetujui kepada petugas untuk serah terima fisik barang.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ─── 3. INFORMASI & KONTAK RESMI SEKOLAH ─────────── -->
            <section class="bg-white rounded-2xl border border-[#D9E4E2] shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="px-6 sm:px-8 py-5 border-b border-[#D9E4E2] flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[#F0FDFA] text-[#0F766E] flex items-center justify-center border border-[#CCFBF1] shrink-0">
                            <span class="material-symbols-outlined text-[22px]">school</span>
                        </div>
                        <h2 class="text-sm sm:text-base font-bold tracking-wide text-[#1F2937] uppercase">
                            Informasi Utama &amp; Kontak Resmi
                        </h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#DCFCE7] text-[#166534] text-xs font-semibold border border-[#16A34A]/30">
                        <span class="w-2 h-2 rounded-full bg-[#16A34A]"></span>
                        Akreditasi A (Unggul)
                    </span>
                </div>

                <!-- Body: Grid 2 kolom di desktop, 1 kolom di mobile -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    <!-- Kiri -->
                    <div class="px-6 sm:px-8 py-6 space-y-6">
                        <!-- Nama Sekolah -->
                        <div class="space-y-1">
                            <span class="text-[11px] text-[#6B7280] uppercase tracking-wider block font-semibold">Nama Sekolah</span>
                            <p class="text-xl sm:text-2xl font-bold text-[#1F2937] tracking-tight leading-snug">
                                {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 BABAKANCIKAO' }}
                            </p>
                            <p class="text-xs text-[#6B7280] leading-relaxed">
                                {{ $schoolProfile->tagline ?? 'Mewujudkan Insan Berakhlak Mulia, Cerdas, Terampil, dan Berbudaya Lingkungan.' }}
                            </p>
                        </div>

                        <!-- Telepon -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[#6B7280] text-[11px] uppercase tracking-wider font-semibold">
                                <span class="material-symbols-outlined text-[15px] text-[#0F766E]">call</span>
                                <span>Nomor Telepon / HP Resmi</span>
                            </div>
                            <p class="text-base font-semibold text-[#1F2937]">
                                {{ $schoolProfile->telepon ?? '(0264) 8301234' }}
                            </p>
                        </div>

                        <!-- Alamat -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[#6B7280] text-[11px] uppercase tracking-wider font-semibold">
                                <span class="material-symbols-outlined text-[15px] text-[#0F766E]">location_on</span>
                                <span>Alamat Lengkap Sekolah</span>
                            </div>
                            <p class="text-sm text-[#1F2937] leading-relaxed">
                                {{ $schoolProfile->alamat ?? 'Jl. Raya Babakancikao No. 45, Purwakarta, Jawa Barat 41151' }}
                                @if(!empty($schoolProfile->kabupaten_kota) || !empty($schoolProfile->provinsi))
                                    <br><span class="text-xs text-[#6B7280]">{{ $schoolProfile->kabupaten_kota ?? '' }}{{ (!empty($schoolProfile->kabupaten_kota) && !empty($schoolProfile->provinsi)) ? ', ' : '' }}{{ $schoolProfile->provinsi ?? '' }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Kanan – dengan border kiri di md ke atas, border atas di mobile -->
                    <div class="px-6 sm:px-8 py-6 space-y-6 border-t border-[#D9E4E2] md:border-t-0 md:border-l md:border-[#D9E4E2]">
                        <!-- NPSN -->
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-1.5 text-[#6B7280] text-[11px] uppercase tracking-wider font-semibold">
                                <span class="material-symbols-outlined text-[15px] text-[#0F766E]">verified</span>
                                <span>NPSN</span>
                            </div>
                            <div>
                                <span class="inline-block text-base font-bold text-[#0F766E] bg-[#F0FDFA] px-3 py-1 rounded-lg border border-[#CCFBF1]">
                                    {{ $schoolProfile->npsn ?? '20205892' }}
                                </span>
                            </div>
                            <p class="text-xs text-[#6B7280]">
                                Terdaftar resmi di Kemendikbudristek &amp; Disdik {{ $schoolProfile->kabupaten_kota ?? 'Kab. Purwakarta' }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[#6B7280] text-[11px] uppercase tracking-wider font-semibold">
                                <span class="material-symbols-outlined text-[15px] text-[#0F766E]">mail</span>
                                <span>Email Resmi Sekolah</span>
                            </div>
                            <p class="text-sm sm:text-base font-semibold text-[#0F766E] break-all">
                                <a href="mailto:{{ $schoolProfile->email ?? 'info@smpn3babakancikao.sch.id' }}" class="hover:underline">
                                    {{ $schoolProfile->email ?? 'info@smpn3babakancikao.sch.id' }}
                                </a>
                            </p>
                        </div>

                        <!-- Jam Layanan -->
                        <div class="p-4 rounded-xl bg-[#F0F4F3] border border-[#D9E4E2] flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px] text-[#0F766E] shrink-0">schedule</span>
                            <div class="space-y-0.5 min-w-0">
                                <p class="text-xs font-semibold text-[#1F2937]">Jam Layanan Kantor Sarpras</p>
                                <p class="text-xs text-[#6B7280]">Senin s.d. Jumat, Pukul 07.00 – 15.30 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ─── 4. DENAH SEKOLAH ────────────────────────────── -->
            <section id="denah-sekolah" class="bg-white rounded-2xl border border-[#D9E4E2] shadow-sm overflow-hidden">
                <!-- Header dengan tombol unduh -->
                <div class="px-6 sm:px-8 py-5 border-b border-[#D9E4E2] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#0F766E] text-[22px] sm:text-[24px]">map</span>
                            <h2 class="text-lg sm:text-xl font-bold text-[#1F2937] tracking-tight">Denah Sekolah</h2>
                        </div>
                        <p class="text-xs sm:text-sm text-[#6B7280]">
                            Informasi tata letak ruang dan fasilitas yang ada di lingkungan SMPN 3 Babakancikao.
                        </p>
                    </div>
                    @if(!empty($schoolProfile->denah) && file_exists(public_path($schoolProfile->denah)))
                        <a href="{{ asset($schoolProfile->denah) }}"
                           download="Denah-{{ \Illuminate\Support\Str::slug($schoolProfile->nama_sekolah ?? 'SMPN-3-Babakancikao') }}"
                           target="_blank"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#0F766E] text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-[#115E59] active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-[#CCFBF1] transition-colors shrink-0">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Unduh Denah</span>
                        </a>
                    @else
                        <button type="button"
                                onclick="showDenahNotice()"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#E5E7EB] hover:bg-[#D1D5DB] text-[#6B7280] text-sm font-semibold rounded-lg transition-colors shrink-0"
                                title="Denah belum tersedia">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Unduh Denah</span>
                        </button>
                    @endif
                </div>

                <!-- Gambar Denah -->
                <div class="p-4 sm:p-6">
                    @if(!empty($schoolProfile->denah) && file_exists(public_path($schoolProfile->denah)))
                        <div class="relative w-full rounded-xl overflow-hidden border border-[#D9E4E2] bg-white group cursor-pointer shadow-sm"
                             onclick="previewDenah('{{ asset($schoolProfile->denah) }}', '{{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}')">
                            <img alt="Denah Arsitektur Kampus {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}"
                                 class="w-full h-auto object-contain block mx-auto transition-transform duration-300 group-hover:scale-[1.005]"
                                 src="{{ asset($schoolProfile->denah) }}">
                            <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-md text-xs text-[#4B5563] border border-[#D9E4E2] shadow-sm">
                                Skala 1:200 • Update Sarpras {{ date('Y') }}
                            </div>
                            <div class="absolute inset-0 bg-black/15 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5 pointer-events-none">
                                <span class="material-symbols-outlined text-[22px]">zoom_in</span>
                                <span>Klik untuk Memperbesar</span>
                            </div>
                        </div>
                    @else
                        <div class="w-full rounded-xl border border-dashed border-[#D9E4E2] bg-[#F8FAFA] py-12 sm:py-16 text-center flex flex-col items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-[#F0FDFA] border border-[#CCFBF1] flex items-center justify-center text-[#0F766E]">
                                <span class="material-symbols-outlined text-[30px]">map</span>
                            </div>
                            <h3 class="text-sm sm:text-base font-bold text-[#1F2937]">Denah Sekolah Belum Tersedia</h3>
                            <p class="text-xs text-[#6B7280] max-w-sm mx-auto leading-relaxed">
                                Admin sekolah belum mengunggah file denah tata letak kampus {{ $schoolProfile->nama_sekolah ?? 'SMPN 3 Babakancikao' }}.
                            </p>
                        </div>
                    @endif
                </div>
            </section>

        </div><!-- end flex flex-col gap -->
    </div><!-- end max-w-7xl -->
</div><!-- end w-full -->

@push('scripts')
<script>
    function previewDenah(url, title) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title ? 'Denah ' + title : 'Denah Kampus & Tata Letak Ruangan',
                imageUrl: url,
                imageAlt: 'Denah Kampus',
                confirmButtonColor: '#0F766E',
                confirmButtonText: 'Tutup',
                showCancelButton: true,
                cancelButtonText: 'Unduh Denah',
                cancelButtonColor: '#115E59',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-4',
                    title: 'text-sm font-bold text-[#1F2937] mb-2',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white',
                    cancelButton: 'px-4 py-2 rounded-lg text-xs font-semibold text-white'
                }
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Denah-Sekolah';
                    a.target = '_blank';
                    a.click();
                }
            });
        }
    }

    function showDenahNotice() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Denah Belum Tersedia',
                text: 'Admin sekolah belum mengunggah berkas denah tata letak kampus sekolah.',
                confirmButtonColor: '#0F766E',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-2xl font-sans shadow-xl border border-[#D9E4E2] p-5 text-center',
                    title: 'text-sm font-bold text-[#1F2937] m-0 mb-2',
                    htmlContainer: 'text-xs text-[#6B7280] m-0 mb-4 leading-relaxed',
                    confirmButton: 'px-4 py-2 rounded-lg text-xs font-semibold bg-[#0F766E] text-white'
                }
            });
        } else {
            alert('Admin sekolah belum mengunggah berkas denah tata letak kampus sekolah.');
        }
    }
</script>
@endpush
@endsection

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="w-full flex items-center justify-between">

        {{-- Tampilan Mobile --}}
        <div class="flex gap-2 items-center justify-between w-full sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-[#9CA3AF] bg-[#F3F7F6] border border-[#D9E4E2] cursor-not-allowed rounded-lg select-none">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-[#1F2937] bg-white border border-[#D9E4E2] rounded-lg hover:bg-[#F0FDFA] hover:text-[#0F766E] transition-colors shadow-xs">
                    Sebelumnya
                </a>
            @endif

            <span class="text-xs text-[#6B7280]">
                Hal. <span class="font-bold text-[#1F2937]">{{ $paginator->currentPage() }}</span> dari <span class="font-bold text-[#1F2937]">{{ $paginator->lastPage() }}</span>
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-[#1F2937] bg-white border border-[#D9E4E2] rounded-lg hover:bg-[#F0FDFA] hover:text-[#0F766E] transition-colors shadow-xs">
                    Berikutnya
                </a>
            @else
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-[#9CA3AF] bg-[#F3F7F6] border border-[#D9E4E2] cursor-not-allowed rounded-lg select-none">
                    Berikutnya
                </span>
            @endif
        </div>

        {{-- Tampilan Desktop (Kiri: Teks Bahasa Indonesia, Kanan: Navigasi Tombol) --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
            <div>
                <p class="text-xs text-[#6B7280]">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-[#1F2937]">{{ $paginator->firstItem() }}</span>
                        sampai
                        <span class="font-semibold text-[#1F2937]">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-semibold text-[#1F2937]">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="inline-flex items-center -space-x-px rounded-lg shadow-xs overflow-hidden border border-[#D9E4E2]">

                    {{-- Tombol Sebelumnya --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Sebelumnya" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-[#9CA3AF] bg-[#F9FAFB] cursor-not-allowed border-r border-[#D9E4E2] select-none">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-[#4B5563] bg-white hover:bg-[#F0FDFA] hover:text-[#0F766E] border-r border-[#D9E4E2] transition-colors" aria-label="Sebelumnya">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </a>
                    @endif

                    {{-- Angka Halaman --}}
                    @foreach ($elements as $element)
                        {{-- Separator Titik Tiga --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-[#9CA3AF] bg-[#F9FAFB] border-r border-[#D9E4E2] select-none">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Tautan Halaman --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-white bg-[#0F766E] border-r border-[#0F766E] select-none">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-medium text-[#4B5563] bg-white hover:bg-[#F0FDFA] hover:text-[#0F766E] border-r border-[#D9E4E2] transition-colors" aria-label="Halaman {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Tombol Berikutnya --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-[#4B5563] bg-white hover:bg-[#F0FDFA] hover:text-[#0F766E] transition-colors" aria-label="Berikutnya">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Berikutnya" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-[#9CA3AF] bg-[#F9FAFB] cursor-not-allowed select-none">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif


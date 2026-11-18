{{-- Tampilan paginasi sesuai desain, dipakai otomatis oleh {{ $data->links() }} --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <p class="text-kecil text-cokelat-600">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="flex size-9 items-center justify-center rounded-kecil border border-garis-kuat opacity-45" aria-disabled="true" aria-label="Halaman sebelumnya">
                    <x-ikon nama="panah-kiri" class="size-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex size-9 items-center justify-center rounded-kecil border border-garis-kuat transition hover:border-cokelat hover:bg-putih" aria-label="Halaman sebelumnya">
                    <x-ikon nama="panah-kiri" class="size-4" />
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="flex size-9 items-center justify-center text-label text-cokelat-600">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="flex size-9 items-center justify-center rounded-kecil bg-cokelat text-label font-semibold text-di-gelap">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex size-9 items-center justify-center rounded-kecil border border-garis-kuat text-label font-semibold text-cokelat transition hover:border-cokelat hover:bg-putih" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex size-9 items-center justify-center rounded-kecil border border-garis-kuat transition hover:border-cokelat hover:bg-putih" aria-label="Halaman berikutnya">
                    <x-ikon nama="panah-kanan" class="size-4" />
                </a>
            @else
                <span class="flex size-9 items-center justify-center rounded-kecil border border-garis-kuat opacity-45" aria-disabled="true" aria-label="Halaman berikutnya">
                    <x-ikon nama="panah-kanan" class="size-4" />
                </span>
            @endif
        </div>
    </nav>
@endif

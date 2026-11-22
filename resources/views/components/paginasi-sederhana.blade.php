{{--
    Paginasi sederhana untuk daftar artikel (sesuai desain):
    "Halaman 1 dari 2" + tombol Halaman sebelumnya / berikutnya.
    Dipakai dengan: {{ $artikel->links('components.paginasi-sederhana') }}
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman artikel" class="flex flex-wrap items-center gap-3">
        <p class="text-kecil text-cokelat-600">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</p>
        @unless ($paginator->onFirstPage())
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="tombol tombol-garis tombol-kecil">
                <x-ikon nama="panah-kiri" class="size-4" /> Halaman sebelumnya
            </a>
        @endunless
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="tombol tombol-garis tombol-kecil">
                Halaman berikutnya <x-ikon nama="panah-kanan" class="size-4" />
            </a>
        @endif
    </nav>
@endif

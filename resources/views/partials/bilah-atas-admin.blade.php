@php use App\Support\Navigasi; @endphp

<div class="sticky top-0 z-30 flex h-[72px] items-center justify-between gap-4 border-b border-garis bg-ivory/95 px-5 backdrop-blur md:px-10 xl:px-12">
    <div class="flex min-w-0 items-center gap-3">
        {{-- Tombol buka sidebar di layar kecil --}}
        <button type="button" class="tombol-ikon lg:hidden" x-on:click="sidebarTerbuka = true" aria-label="Buka menu pengelola">
            <x-ikon nama="menu" />
        </button>

        <nav aria-label="Breadcrumb" class="flex min-w-0 items-center gap-2 text-kecil">
            <a href="{{ Navigasi::url('admin.dashboard') }}" class="hidden text-cokelat-600 transition hover:text-maroon sm:inline">Pengelola</a>
            <x-ikon nama="chevron-kanan" class="hidden size-3.5 text-cokelat-600 sm:block" />
            <span class="truncate text-label font-semibold text-cokelat" aria-current="page">@yield('halaman', 'Dashboard')</span>
        </nav>
    </div>

    <div class="flex items-center gap-6">
        <form method="GET" action="{{ Navigasi::url('admin.reservasi.index') }}" role="search"
              class="hidden h-10 w-[340px] items-center gap-2.5 rounded-input border border-garis-kuat bg-putih px-3.5 transition focus-within:border-cokelat focus-within:ring-1 focus-within:ring-cokelat xl:flex">
            <x-ikon nama="cari" class="size-4 text-cokelat-600" />
            <label for="cari-admin" class="sr-only">Cari reservasi</label>
            <input id="cari-admin" type="search" name="cari" value="{{ request('cari') }}"
                   placeholder="Cari kode booking atau nama pengunjung"
                   class="h-full flex-1 border-0 bg-transparent p-0 text-kecil text-cokelat placeholder:text-cokelat-600 focus:ring-0">
        </form>
        <p class="hidden whitespace-nowrap text-kecil text-cokelat-600 md:block">{{ now()->translatedFormat('l, j M Y') }}</p>
    </div>
</div>

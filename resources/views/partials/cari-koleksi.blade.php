{{-- Tombol cari di navbar desktop. Membuka kolom pencarian yang mengirim ?cari= ke katalog. --}}
<div class="relative" x-on:click.outside="cariTerbuka = false">
    <button type="button" class="tombol-ikon" aria-label="Cari koleksi"
            x-on:click="cariTerbuka = ! cariTerbuka; $nextTick(() => cariTerbuka && $refs.kolomCari.focus())"
            :aria-expanded="cariTerbuka">
        <x-ikon nama="cari" />
    </button>

    <form x-show="cariTerbuka" x-cloak
          x-transition:enter="transition duration-200 ease-out"
          x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
          x-transition:enter-end="opacity-100 translate-y-0 scale-100"
          x-transition:leave="transition duration-150 ease-in"
          x-transition:leave-end="opacity-0 -translate-y-2"
          method="GET" action="{{ \App\Support\Navigasi::url('koleksi.index') }}"
          class="absolute right-0 top-[calc(100%+10px)] z-50 flex w-[340px] items-center gap-2 rounded-input border border-garis-kuat bg-putih p-2 shadow-angkat"
          role="search">
        <label for="cari-navbar" class="sr-only">Cari koleksi</label>
        <x-ikon nama="cari" class="ml-2 size-4 text-cokelat-600" />
        <input id="cari-navbar" x-ref="kolomCari" type="search" name="cari" placeholder="Cari arca, prasasti, gerabah…"
               class="h-10 flex-1 border-0 bg-transparent px-1 text-kecil text-cokelat placeholder:text-cokelat-600 focus:ring-0">
        <button type="submit" class="tombol tombol-utama tombol-kecil h-9 px-3">Cari</button>
    </form>
</div>

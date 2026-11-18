@php
    use App\Support\Navigasi;

    $menuPublik = config('menu.publik');
    $menuAkun = config('menu.akun');
    $user = auth()->user();
    $urlAkun = $user ? Navigasi::url($user->isAdmin() ? 'admin.dashboard' : 'dashboard') : null;

    // Index menu yang aktif, dipakai untuk posisi awal "pil" penanda
    $indexAktif = collect($menuPublik)->search(fn ($m) => Navigasi::aktif($m['aktif']));
@endphp

<header x-data="{ menuTerbuka: false, cariTerbuka: false, tergulir: false }"
        x-on:scroll.window.throttle.50ms="tergulir = window.scrollY > 8"
        x-on:keydown.escape.window="menuTerbuka = false; cariTerbuka = false"
        class="sticky top-0 z-40 transition-colors duration-300"
        :class="tergulir ? 'bg-ivory/90 shadow-[0_1px_0_#E2D7C6] backdrop-blur' : 'bg-ivory'">

    {{-- ===== Desktop (≥1280px) ===== --}}
    <div class="wadah hidden h-[84px] items-center justify-between gap-6 xl:flex">
        <a href="{{ Navigasi::url('beranda') }}" class="shrink-0 rounded-kecil focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cokelat focus-visible:ring-offset-2">
            <x-wordmark />
        </a>

        {{-- Menu pil gelap. Pil terang bergeser mengikuti menu yang disorot mouse. --}}
        <nav aria-label="Menu utama"
             x-data="{
                 aktif: {{ $indexAktif === false ? -1 : $indexAktif }},
                 sorot: {{ $indexAktif === false ? -1 : $indexAktif }},
                 pil: { left: 0, width: 0 },
                 ukur() {
                     const item = this.$refs['menu' + this.sorot];
                     this.pil = item ? { left: item.offsetLeft, width: item.offsetWidth } : { left: 0, width: 0 };
                 },
             }"
             x-init="$nextTick(() => ukur()); window.addEventListener('resize', () => ukur())"
             x-on:mouseleave="sorot = aktif; ukur()"
             class="relative flex gap-0.5 rounded-full bg-cokelat p-[5px]">
            <span class="absolute bottom-[5px] top-[5px] rounded-full bg-ivory transition-all duration-300 ease-out"
                  :style="`left: ${pil.left}px; width: ${pil.width}px; opacity: ${sorot < 0 ? 0 : 1}`"
                  aria-hidden="true"></span>

            @foreach ($menuPublik as $i => $menu)
                <a href="{{ Navigasi::url($menu['route']) }}"
                   x-ref="menu{{ $i }}"
                   x-on:mouseenter="sorot = {{ $i }}; ukur()"
                   x-on:focus="sorot = {{ $i }}; ukur()"
                   @if (Navigasi::aktif($menu['aktif'])) aria-current="page" @endif
                   class="relative flex h-10 items-center whitespace-nowrap rounded-full px-4 text-label font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emas"
                   :class="sorot === {{ $i }} ? 'text-cokelat' : 'text-di-gelap'">
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex shrink-0 items-center gap-2.5">
            @include('partials.cari-koleksi')

            @auth
                <a href="{{ $urlAkun }}" class="tombol tombol-garis tombol-kecil">
                    {{ $user->isAdmin() ? 'Ruang pengelola' : 'Akun saya' }}
                </a>
            @else
                <a href="{{ route('login') }}" class="tombol tombol-garis tombol-kecil">Masuk</a>
                <a href="{{ route('register') }}" class="tombol tombol-utama tombol-kecil">Daftar</a>
            @endauth
        </div>
    </div>

    {{-- ===== Ponsel dan tablet (<1280px) ===== --}}
    <div class="flex h-[72px] items-center justify-between border-b border-garis px-5 md:px-10 xl:hidden">
        <a href="{{ Navigasi::url('beranda') }}">
            <x-wordmark ukuran="kecil" />
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ Navigasi::url('koleksi.index') }}" class="tombol-ikon" aria-label="Cari koleksi">
                <x-ikon nama="cari" />
            </a>
            <button type="button" x-on:click="menuTerbuka = true"
                    class="tombol tombol-garis h-11 gap-2 pl-3 pr-3.5 text-label"
                    aria-controls="menu-ponsel" :aria-expanded="menuTerbuka">
                <x-ikon nama="menu" />
                Menu
            </button>
        </div>
    </div>

    {{-- Lembar menu ponsel --}}
    <div id="menu-ponsel"
         x-show="menuTerbuka"
         x-trap.noscroll="menuTerbuka"
         x-transition:enter="transition duration-300 ease-out"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition duration-200 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="fixed inset-0 z-50 overflow-y-auto bg-cokelat xl:hidden"
         role="dialog" aria-modal="true" aria-label="Menu"
         x-cloak>
        <div class="flex items-center justify-between px-5 py-3.5 md:px-10">
            <x-wordmark latar="gelap" ukuran="kecil" />
            <button type="button" x-on:click="menuTerbuka = false"
                    class="tombol h-11 gap-2 border border-emas pl-3 pr-3.5 text-label text-ivory hover:bg-ivory/10">
                <x-ikon nama="tutup" />
                Tutup
            </button>
        </div>

        <nav class="flex flex-col px-5 pt-4 md:px-10" aria-label="Menu utama ponsel">
            @foreach ($menuPublik as $i => $menu)
                <a href="{{ Navigasi::url($menu['route']) }}"
                   class="flex items-center justify-between border-b py-3 font-display text-h2-ponsel font-semibold text-ivory transition-colors hover:text-emas
                          {{ $loop->first ? 'border-emas' : 'border-ivory/60' }}"
                   x-show="menuTerbuka"
                   x-transition:enter="transition duration-300 ease-out"
                   x-transition:enter-start="opacity-0 translate-x-6"
                   x-transition:enter-end="opacity-100 translate-x-0"
                   style="transition-delay: {{ 60 + $i * 40 }}ms">
                    {{ $menu['label'] }}
                    @if (Navigasi::aktif($menu['aktif']))
                        <x-belah-ketupat />
                    @endif
                </a>
            @endforeach
        </nav>

        @auth
            <div class="flex flex-col gap-1 px-5 pt-8 md:px-10">
                <p class="flex items-center gap-2.5 pb-2 text-label font-semibold text-emas">
                    <x-belah-ketupat />
                    {{ $user->isAdmin() ? 'Ruang pengelola' : 'Akun saya' }} · {{ $user->name }}
                </p>
                @if ($user->isAdmin())
                    <a href="{{ $urlAkun }}" class="py-[11px] text-isi text-ivory hover:text-emas">Dashboard pengelola</a>
                @else
                    @foreach ($menuAkun as $menu)
                        <a href="{{ Navigasi::url($menu['route']) }}" class="py-[11px] text-isi text-ivory hover:text-emas">{{ $menu['label'] }}</a>
                    @endforeach
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}" class="px-5 pb-10 pt-6 md:px-10">
                @csrf
                <button type="submit" class="tombol w-full border border-emas text-ivory hover:bg-ivory/10">Keluar</button>
            </form>
        @else
            <div class="flex flex-col gap-3 px-5 pb-10 pt-8 md:px-10">
                <a href="{{ route('login') }}" class="tombol w-full border border-emas text-ivory hover:bg-ivory/10">Masuk</a>
                <a href="{{ route('register') }}" class="tombol tombol-terang w-full">Daftar</a>
            </div>
        @endauth
    </div>
</header>

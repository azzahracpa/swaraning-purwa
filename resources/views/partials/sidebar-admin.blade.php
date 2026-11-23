@php
    use App\Support\Navigasi;

    $grupMenu = config('menu.admin');
    $admin = auth()->user();
    // $jumlahMenu berisi angka badge, dikirim view composer di AppServiceProvider
@endphp

<div class="flex h-full flex-col gap-6 px-4 pb-6 pt-7">
    <div class="flex flex-col gap-2.5 pl-3">
        <a href="{{ Navigasi::url('admin.dashboard') }}"><x-wordmark latar="gelap" ukuran="kecil" /></a>
        <p class="flex items-center gap-2 text-label-kecil font-semibold uppercase tracking-[0.12em] text-emas">
            <span class="size-[5px] rotate-45 bg-emas"></span>
            Ruang pengelola
        </p>
    </div>

    <div class="h-px w-full bg-ivory/10"></div>

    <nav class="flex flex-col gap-5" aria-label="Menu pengelola">
        @foreach ($grupMenu as $judulGrup => $daftarMenu)
            <div class="flex flex-col gap-0.5">
                <p class="pb-1.5 pl-3 text-label-kecil font-semibold uppercase tracking-[0.1em] text-di-gelap/60">{{ $judulGrup }}</p>
                @foreach ($daftarMenu as $menu)
                    @php
                        $aktif = Navigasi::aktif($menu['aktif']);
                        $jumlah = isset($menu['jumlah']) ? ($jumlahMenu[$menu['jumlah']] ?? 0) : 0;
                    @endphp
                    <a href="{{ Navigasi::url($menu['route']) }}"
                       @if ($aktif) aria-current="page" @endif
                       class="group flex h-11 items-center gap-3 rounded-input px-3 transition-colors duration-200 {{ $aktif ? 'bg-white/[0.07]' : 'hover:bg-white/[0.04]' }}">
                        <span class="size-[7px] rotate-45 bg-emas transition-transform duration-200 {{ $aktif ? '' : 'scale-0 group-hover:scale-75' }}"></span>
                        <x-ikon :nama="$menu['ikon']" class="size-[18px] {{ $aktif ? 'text-emas' : 'text-di-gelap opacity-80' }}" />
                        <span class="flex-1 {{ $aktif ? 'text-label font-semibold text-emas' : 'text-kecil text-di-gelap' }}">{{ $menu['label'] }}</span>
                        @if ($jumlah > 0)
                            <span class="rounded-[10px] bg-emas px-2 py-0.5 text-label-kecil font-semibold text-cokelat">{{ $jumlah }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="min-h-6 flex-1"></div>

    <div class="h-12 rounded-input bg-kawung bg-[length:50px_50px] opacity-[0.16]" aria-hidden="true"></div>

    <div class="flex items-center gap-3 px-3 py-1">
        <x-avatar-inisial :user="$admin" />
        <div class="flex min-w-0 flex-col gap-0.5 text-di-gelap">
            <p class="truncate text-label font-semibold">{{ $admin->name }}</p>
            <p class="text-kecil opacity-70">Pengelola</p>
        </div>
    </div>

    <div class="flex flex-col gap-0.5">
        <a href="{{ Navigasi::url('beranda') }}" class="group flex h-11 items-center gap-3 rounded-input px-3 text-kecil text-di-gelap transition hover:bg-white/[0.04]">
            <span class="size-[7px]"></span>
            <x-ikon nama="panah-kanan" class="size-[18px] opacity-80 transition-transform group-hover:translate-x-0.5" />
            Lihat situs publik
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex h-11 w-full items-center gap-3 rounded-input px-3 text-left text-kecil text-di-gelap transition hover:bg-white/[0.04]">
                <span class="size-[7px]"></span>
                <x-ikon nama="keluar" class="size-[18px] opacity-80" />
                Keluar
            </button>
        </form>
    </div>
</div>

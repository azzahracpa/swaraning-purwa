@php
    use App\Support\Navigasi;

    $menuAkun = config('menu.akun');
@endphp

{{-- Desktop: rel navigasi di kolom kiri --}}
<aside class="hidden lg:block" aria-label="Navigasi akun">
    <div class="sticky top-[108px] flex flex-col">
        <div class="flex flex-col gap-1 pb-5">
            <p class="text-kecil text-cokelat-600">Masuk sebagai</p>
            <p class="teks-h3">{{ auth()->user()->name }}</p>
        </div>

        <nav class="flex flex-col border-t border-cokelat">
            @foreach ($menuAkun as $menu)
                @php $aktif = Navigasi::aktif($menu['aktif']); @endphp
                <a href="{{ Navigasi::url($menu['route']) }}"
                   @if ($aktif) aria-current="page" @endif
                   class="group flex h-[52px] items-center gap-3 border-b border-garis transition-colors">
                    <span class="flex size-3 items-center justify-center">
                        <span class="size-[7px] rotate-45 transition-all duration-200 {{ $aktif ? 'bg-emas' : 'scale-0 bg-emas group-hover:scale-100' }}"></span>
                    </span>
                    <span class="transition-transform duration-200 group-hover:translate-x-1 {{ $aktif ? 'text-label font-semibold text-maroon' : 'text-isi text-cokelat' }}">
                        {{ $menu['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="pt-5">
            @csrf
            <button type="submit" class="flex items-center gap-2.5 text-label font-semibold text-cokelat-600 transition hover:text-maroon">
                <x-ikon nama="keluar" />
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- Ponsel: deretan chip "Bagian akun" yang bisa digeser --}}
<nav class="-mx-5 md:-mx-10 lg:hidden" aria-label="Bagian akun">
    <p class="px-5 pb-2 text-label font-semibold text-cokelat-600 md:px-10">Bagian akun</p>
    <div x-data x-geser class="flex gap-2 overflow-x-auto px-5 pb-1 md:px-10">
        @foreach ($menuAkun as $menu)
            @php $aktif = Navigasi::aktif($menu['aktif']); @endphp
            <a href="{{ Navigasi::url($menu['route']) }}"
               @if ($aktif) aria-current="page" @endif
               class="flex h-10 shrink-0 items-center rounded-full border px-4 text-label font-semibold transition
                      {{ $aktif ? 'border-cokelat bg-cokelat text-ivory' : 'border-garis-kuat bg-putih text-cokelat hover:border-cokelat' }}">
                {{ $menu['label'] }}
            </a>
        @endforeach
    </div>
</nav>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head', ['judulHalaman' => $judul ?? null])
</head>
<body>
    {{--
        Layout halaman masuk dan daftar: panel cokelat di kiri (desktop),
        formulir di kanan. Isi panel kiri bisa diganti lewat slot "panel".
    --}}
    <div class="grid min-h-screen lg:grid-cols-[600px_1fr]">
        <aside class="relative hidden flex-col justify-between overflow-hidden bg-cokelat px-16 pb-14 pt-12 text-ivory lg:flex">
            <div class="absolute inset-0 bg-kawung bg-[length:64px_64px] opacity-[0.05]" aria-hidden="true"></div>

            <a href="{{ \App\Support\Navigasi::url('beranda') }}" class="relative self-start">
                <x-wordmark latar="gelap" />
            </a>

            <div class="relative">
                @isset($panel)
                    {{ $panel }}
                @else
                    <p class="font-display text-display-l-ponsel font-medium uppercase xl:text-[64px] xl:leading-[64px]">
                        Satu akun untuk favorit, <span class="italic text-emas">reservasi</span>, dan kuis.
                    </p>
                    <ol class="mt-10 flex flex-col border-t border-ivory/60">
                        @foreach (['Simpan koleksi favorit', 'Ajukan dan pantau reservasi kunjungan', 'Kerjakan kuis dan lihat skor'] as $i => $poin)
                            <li class="flex items-center gap-4 border-b border-ivory/60 py-3.5" x-data x-muncul="{{ $i * 120 }}">
                                <span class="font-display text-angka-sedang text-emas">0{{ $i + 1 }}</span>
                                <span class="text-label font-semibold">{{ $poin }}</span>
                            </li>
                        @endforeach
                    </ol>
                @endisset
            </div>

            <p class="relative text-kecil text-di-gelap">Katalog, artikel, dan profil museum tetap bisa dibaca tanpa akun.</p>
        </aside>

        <main class="flex flex-col">
            <div class="flex h-[72px] items-center border-b border-garis px-5 md:px-10 lg:hidden">
                <a href="{{ \App\Support\Navigasi::url('beranda') }}"><x-wordmark ukuran="kecil" /></a>
            </div>

            <div class="flex flex-1 items-center justify-center px-5 py-12 md:px-10">
                <div class="w-full max-w-[400px] animate-muncul-naik">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>
</body>
</html>

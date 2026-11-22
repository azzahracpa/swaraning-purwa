{{--
    Tampilan halaman galat (403, 404, 419, 500) dengan gaya situs.
    Sengaja tidak memakai navbar lengkap supaya tetap tampil walau ada masalah data.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('kode') · {{ config('app.name', 'Swaraning Purwa') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/ui/tanda.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-ivory">
    <header class="wadah flex h-[84px] items-center">
        <a href="{{ url('/') }}"><x-wordmark /></a>
    </header>

    <main class="wadah flex flex-1 flex-col items-start justify-center gap-6 py-16 lg:flex-row lg:items-center lg:justify-between lg:gap-16">
        <div class="flex max-w-[640px] flex-col gap-6">
            <p class="flex items-center gap-3">
                <span class="font-display text-angka text-emas-700">@yield('kode')</span>
                <span class="size-[7px] rotate-45 bg-emas"></span>
                <span class="text-label font-semibold">@yield('label')</span>
            </p>
            <h1 class="teks-display-l uppercase">@yield('judul')</h1>
            <p class="teks-lead text-cokelat-600">@yield('pesan')</p>
            <div class="flex flex-wrap gap-3">
                @yield('aksi')
                <a href="{{ url('/') }}" class="tombol tombol-garis">Kembali ke beranda</a>
            </div>
        </div>

        {{-- Ornamen relung dengan angka galat --}}
        <div class="relative hidden h-[360px] w-[280px] shrink-0 overflow-hidden rounded-relung bg-cokelat lg:block" aria-hidden="true">
            <div class="absolute inset-0 bg-kawung bg-[length:40px_40px] opacity-[0.16]"></div>
            <p class="absolute inset-x-0 bottom-10 text-center font-display text-[120px] font-medium leading-none text-emas">@yield('kode')</p>
        </div>
    </main>

    <footer class="h-2.5 w-full bg-ivory bg-tumpal bg-repeat-x bg-[length:auto_10px]" aria-hidden="true"></footer>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="flex min-h-screen flex-col">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-kecil focus:bg-cokelat focus:px-4 focus:py-2 focus:text-ivory">
        Lewati ke konten
    </a>

    @include('partials.navbar')

    <main id="konten" class="wadah flex-1 pb-24 pt-6 lg:pb-32">
        {{-- Bagian atas halaman akun: breadcrumb, judul besar, aksi utama --}}
        @hasSection('kepala')
            <div class="pb-8 lg:pb-12">
                @yield('kepala')
            </div>
        @endif

        <x-flash class="mb-8" />

        <div class="flex flex-col gap-8 lg:grid lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-16 xl:gap-[46px]">
            @include('partials.sidebar-akun')

            <div class="min-w-0">
                @yield('content')
            </div>
        </div>
    </main>

    @include('partials.footer')

    <x-dialog-konfirmasi />
    @stack('scripts')
</body>
</html>

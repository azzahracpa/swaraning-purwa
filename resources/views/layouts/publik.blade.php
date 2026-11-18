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

    <main id="konten" class="flex-1">
        @if (session('success') || session('error') || session('info'))
            <div class="wadah pt-6">
                <x-flash />
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    <x-dialog-konfirmasi />
    @stack('scripts')
</body>
</html>

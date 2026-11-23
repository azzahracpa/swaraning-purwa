<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="bg-ivory" x-data="{ sidebarTerbuka: false }" x-on:keydown.escape.window="sidebarTerbuka = false">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-kecil focus:bg-cokelat focus:px-4 focus:py-2 focus:text-ivory">
        Lewati ke konten
    </a>

    <div class="flex min-h-screen">
        {{-- Sidebar tetap di desktop --}}
        <aside class="sticky top-0 hidden h-screen w-[264px] shrink-0 overflow-y-auto bg-cokelat lg:block">
            @include('partials.sidebar-admin')
        </aside>

        {{-- Sidebar laci di layar kecil --}}
        <div x-show="sidebarTerbuka" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu pengelola">
            <div x-show="sidebarTerbuka" x-transition.opacity x-on:click="sidebarTerbuka = false" class="absolute inset-0 bg-cokelat/50"></div>
            <aside x-show="sidebarTerbuka"
                   x-trap.noscroll="sidebarTerbuka"
                   x-transition:enter="transition duration-300 ease-out"
                   x-transition:enter-start="-translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition duration-200 ease-in"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="-translate-x-full"
                   class="relative h-full w-[280px] overflow-y-auto bg-cokelat">
                @include('partials.sidebar-admin')
            </aside>
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
            @include('partials.bilah-atas-admin')

            <main id="konten" class="flex-1 px-5 py-8 md:px-10 xl:px-12 xl:py-10">
                <x-flash class="mb-6" />
                @yield('content')
            </main>

            <footer class="flex flex-col gap-1 border-t border-garis px-5 py-6 text-kecil text-cokelat-600 sm:flex-row sm:justify-between md:px-10 xl:px-12">
                <p>© {{ now()->year }} Swaraning Purwa · Ruang pengelola</p>
                <p>Museum Mpu Purwa, Kota Malang</p>
            </footer>
        </div>
    </div>

    <x-dialog-konfirmasi />
    @stack('scripts')
</body>
</html>

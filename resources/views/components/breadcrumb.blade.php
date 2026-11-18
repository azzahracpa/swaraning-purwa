@props(['items' => []])

{{-- Contoh: <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => null]" /> --}}
<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2 text-kecil']) }}>
    @foreach ($items as $label => $url)
        @if (! $loop->first)
            <x-ikon nama="chevron-kanan" class="size-3.5 text-cokelat-600" />
        @endif
        @if ($url && ! $loop->last)
            <a href="{{ $url }}" class="text-cokelat-600 transition hover:text-maroon">{{ $label }}</a>
        @else
            <span class="font-semibold text-cokelat" @if ($loop->last) aria-current="page" @endif>{{ $label }}</span>
        @endif
    @endforeach
</nav>

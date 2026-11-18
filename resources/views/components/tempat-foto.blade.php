@props(['nada' => 'gelap', 'keterangan' => null, 'label' => 'Foto contoh'])

{{-- Pengganti foto resmi. Ukuran dan radius diatur lewat class saat dipakai. --}}
@php $gelap = $nada === 'gelap'; @endphp

<div {{ $attributes->merge(['class' => 'relative flex flex-col justify-between overflow-hidden rounded-kartu p-3.5 '.($gelap ? 'bg-cokelat' : 'bg-foto')]) }}>
    <div class="absolute inset-0 bg-kawung bg-[length:40px_40px] {{ $gelap ? 'opacity-[0.16]' : 'opacity-[0.18]' }}" aria-hidden="true"></div>
    <x-ikon nama="gambar" class="absolute left-1/2 top-1/2 size-[34px] -translate-x-1/2 -translate-y-1/2 opacity-75 {{ $gelap ? 'text-emas' : 'text-emas-700' }}" />
    <x-penanda-contoh :latar="$gelap ? 'gelap' : 'terang'" :teks="$label" class="relative self-start" />
    @if ($keterangan)
        <p class="relative text-kecil {{ $gelap ? 'text-di-gelap' : 'text-cokelat-600' }}">{{ $keterangan }}</p>
    @endif
</div>

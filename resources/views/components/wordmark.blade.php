@props(['latar' => 'terang', 'ukuran' => 'besar'])

{{-- Nama situs + tanda lengkung (relung). Tanda ini usulan tim, bukan logo resmi museum. --}}
@php
    $gelap = $latar === 'gelap';
    $besar = $ukuran === 'besar';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center '.($besar ? 'gap-3' : 'gap-2.5')]) }}>
    <img src="{{ asset($gelap ? 'images/ui/tanda-gelap.svg' : 'images/ui/tanda.svg') }}" alt=""
         class="{{ $besar ? 'h-11 w-10' : 'h-[33px] w-[30px]' }} shrink-0">
    <span class="flex flex-col gap-0.5 whitespace-nowrap">
        <span class="font-display font-semibold {{ $besar ? 'text-h3' : 'text-h3-ponsel max-[389px]:text-[19px]' }} {{ $gelap ? 'text-ivory' : 'text-cokelat' }}">
            Swaraning Purwa
        </span>
        @if ($besar)
            <span class="teks-label-kecil {{ $gelap ? 'text-di-gelap' : 'text-cokelat-600' }}">
                Museum Mpu Purwa, Kota Malang
            </span>
        @endif
    </span>
</span>

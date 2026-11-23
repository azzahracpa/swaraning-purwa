@props(['artikel', 'nomor'])

{{-- Baris daftar artikel. Seluruh baris adalah tautan ke halaman baca. --}}
<a href="{{ route('artikel.show', $artikel) }}"
   {{ $attributes->merge(['class' => 'group flex items-center gap-4 border-b border-garis py-5 transition-colors hover:bg-putih/60 sm:gap-6 sm:py-6']) }}>
    <span class="w-8 shrink-0 font-display text-angka-sedang font-medium text-emas-700 sm:w-10">
        {{ str_pad($nomor, 2, '0', STR_PAD_LEFT) }}
    </span>
    <span class="flex min-w-0 flex-1 flex-col gap-2">
        <span class="teks-h3 transition-colors group-hover:text-maroon">{{ $artikel->title }}</span>
        <span class="flex flex-wrap items-center gap-2 text-kecil text-cokelat-600">
            {{ $artikel->published_at?->translatedFormat('j F Y') }}
        </span>
    </span>
    <span class="tombol-ikon shrink-0 transition-transform group-hover:translate-x-1 group-hover:border-cokelat group-hover:bg-putih" aria-hidden="true">
        <x-ikon nama="panah-kanan" />
    </span>
</a>

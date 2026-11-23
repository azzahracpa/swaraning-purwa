{{-- Kartu koleksi khusus beranda (seksi Koleksi unggulan). Seluruh kartu adalah tautan. --}}
@php $foto = $koleksi->primaryImage; @endphp

{{-- Route detail koleksi dibuat di modul Katalog, jadi dicek dulu keberadaannya --}}
<a href="{{ Route::has('koleksi.show') ? route('koleksi.show', $koleksi) : '#' }}"
   class="group flex flex-col gap-[18px] focus-visible:outline-none"
   x-data x-miring="4">
    <div class="relative aspect-[302/370] w-full overflow-hidden rounded-relung bg-foto ring-1 ring-transparent transition duration-300 group-hover:ring-emas group-focus-visible:ring-2 group-focus-visible:ring-cokelat">
        @if ($foto)
            <img src="{{ $foto->url }}" alt="{{ $foto->caption ?? $koleksi->name }}" loading="lazy"
                 class="size-full object-cover transition duration-500 group-hover:scale-[1.04]">
        @else
            <x-tempat-foto nada="lembut" class="size-full rounded-none" :keterangan="'Foto '.$koleksi->name" />
        @endif
    </div>

    <div class="flex flex-col gap-2.5 px-1">
        <div class="flex items-center justify-between gap-2">
            <span class="rounded-kecil border border-garis-kuat px-2.5 py-1 teks-label-kecil text-cokelat-600">
                {{ $koleksi->category->name }}
            </span>
        </div>
        <h3 class="teks-h3 transition-colors group-hover:text-maroon">{{ $koleksi->name }}</h3>
        <p class="text-kecil text-cokelat-600">
            {{ collect([$koleksi->historical_period, $koleksi->material])->filter()->implode(' · ') }}
        </p>
    </div>
</a>

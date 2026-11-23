@props(['koleksi'])

{{--
    Kartu koleksi di katalog. Seluruh kartu adalah satu tautan ke detail koleksi.
    Foto bersudut relung; saat disorot judul berubah maroon dan foto sedikit membesar.
    Butuh eager loading: Collection::with(['category', 'primaryImage'])
--}}
@php $foto = $koleksi->primaryImage; @endphp

<a href="{{ route('koleksi.show', $koleksi) }}"
   {{ $attributes->merge(['class' => 'group flex flex-col gap-[18px] rounded-kartu focus-visible:outline-none']) }}>
    <div class="relative aspect-[296/370] w-full overflow-hidden rounded-relung bg-foto ring-1 ring-transparent transition duration-300 group-hover:ring-emas group-focus-visible:ring-2 group-focus-visible:ring-cokelat">
        @if ($foto)
            <img src="{{ $foto->url }}" alt="{{ $foto->caption ?? 'Foto '.$koleksi->name }}" loading="lazy"
                 class="size-full object-cover transition duration-500 group-hover:scale-[1.05]">
        @else
            <x-tempat-foto nada="lembut" class="size-full rounded-none" :keterangan="'Foto '.$koleksi->name" />
            <span class="absolute bottom-3 left-3 rounded-kecil bg-putih/90 px-2.5 py-1 teks-label-kecil text-cokelat-600">Foto belum tersedia</span>
        @endif
    </div>

    <div class="flex flex-col gap-2.5 px-1">
        <div class="flex items-center justify-between gap-2">
            <x-label-kategori>{{ $koleksi->category->name }}</x-label-kategori>
            <x-penanda-contoh teks="data contoh" />
        </div>
        <h3 class="teks-h3 transition-colors group-hover:text-maroon">{{ $koleksi->name }}</h3>
        <p class="text-kecil text-cokelat-600">{{ $koleksi->metaSingkat() }}</p>
    </div>
</a>

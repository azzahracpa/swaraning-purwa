{{-- Label kategori koleksi, misal "Arca" --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-kecil border border-garis-kuat px-2.5 py-1 teks-label-kecil text-cokelat-600']) }}>
    {{ $slot }}
</span>

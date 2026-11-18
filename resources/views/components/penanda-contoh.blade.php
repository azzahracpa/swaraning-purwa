@props(['latar' => 'terang', 'teks' => 'contoh'])

{{-- Wajib dipasang pada data yang belum diverifikasi museum (foto, jam buka, alamat, data koleksi). --}}
<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center rounded-kecil border border-dashed px-[9px] py-1 teks-label-kecil '.($latar === 'gelap'
    ? 'border-emas bg-cokelat text-emas'
    : 'border-emas-700 bg-ivory text-emas-700')]) }}>
    {{ $teks }}
</span>

@props(['nada' => 'netral'])

{{-- Badge kecil di tabel admin (Aktif, Draf, Terbit, Penuh, dsb) --}}
@php
    $kelas = [
        'positif' => 'border-status-disetujui-teks/35 bg-status-disetujui-latar text-status-disetujui-teks',
        'menunggu' => 'border-status-menunggu-teks/35 bg-status-menunggu-latar text-status-menunggu-teks',
        'negatif' => 'border-status-ditolak-teks/35 bg-status-ditolak-latar text-status-ditolak-teks',
        'netral' => 'border-status-dibatalkan-teks/35 bg-status-dibatalkan-latar text-status-dibatalkan-teks',
    ][$nada] ?? '';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex h-7 items-center whitespace-nowrap rounded-kecil border px-2.5 text-label font-semibold {$kelas}"]) }}>
    {{ $slot }}
</span>

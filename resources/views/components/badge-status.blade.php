@props(['status'])

{{-- Status reservasi selalu tampil sebagai teks + ikon, warna hanya pendukung --}}
@php
    $gaya = [
        'pending' => ['Menunggu', 'jam', 'border-status-menunggu-teks/35 bg-status-menunggu-latar text-status-menunggu-teks'],
        'approved' => ['Disetujui', 'centang', 'border-status-disetujui-teks/35 bg-status-disetujui-latar text-status-disetujui-teks'],
        'rejected' => ['Ditolak', 'tutup', 'border-status-ditolak-teks/35 bg-status-ditolak-latar text-status-ditolak-teks'],
        'cancelled' => ['Dibatalkan', 'larangan', 'border-status-dibatalkan-teks/35 bg-status-dibatalkan-latar text-status-dibatalkan-teks'],
    ];
    [$label, $ikon, $kelas] = $gaya[$status] ?? [$status, 'info', 'border-garis-kuat bg-lembut text-cokelat'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex h-7 items-center gap-1.5 whitespace-nowrap rounded-kecil border pl-2 pr-2.5 text-label font-semibold {$kelas}"]) }}>
    <x-ikon :nama="$ikon" class="size-3.5" />
    {{ $label }}
</span>

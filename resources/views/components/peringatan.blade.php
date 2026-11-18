@props(['jenis' => 'sukses', 'judul' => null, 'tutup' => true])

{{-- Pesan sukses / galat / info. Bisa ditutup dan hilang otomatis untuk jenis sukses. --}}
@php
    $gaya = [
        'sukses' => ['centang', 'border-status-disetujui-teks bg-status-disetujui-latar'],
        'galat' => ['peringatan', 'border-status-ditolak-teks bg-status-ditolak-latar'],
        'info' => ['info', 'border-status-menunggu-teks bg-status-menunggu-latar'],
    ];
    [$ikon, $kelas] = $gaya[$jenis] ?? $gaya['info'];
@endphp

<div x-data="{ tampil: true }"
     x-show="tampil"
     x-transition:leave="transition duration-200 ease-in"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     @if ($jenis === 'sukses') x-init="setTimeout(() => tampil = false, 6000)" @endif
     role="{{ $jenis === 'galat' ? 'alert' : 'status' }}"
     {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-input border px-4 py-3.5 text-cokelat animate-muncul-naik {$kelas}"]) }}>
    <x-ikon :nama="$ikon" class="mt-px size-5" />
    <div class="flex min-w-0 flex-1 flex-col gap-0.5">
        @if ($judul)
            <p class="text-label font-semibold">{{ $judul }}</p>
        @endif
        <div class="text-kecil">{{ $slot }}</div>
    </div>
    @if ($tutup)
        <button type="button" x-on:click="tampil = false" class="-m-1 rounded-kecil p-1 opacity-70 transition hover:opacity-100" aria-label="Tutup pesan">
            <x-ikon nama="tutup" class="size-4" />
        </button>
    @endif
</div>

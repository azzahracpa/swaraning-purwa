@props(['judul', 'ikon' => 'info'])

{{-- Tampilan saat data kosong, misal belum ada reservasi atau hasil pencarian kosong --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-4 rounded-kartu border border-dashed border-garis-kuat bg-putih/60 px-6 py-12 text-center']) }}>
    <span class="flex size-14 items-center justify-center rounded-full bg-lembut text-emas-700">
        <x-ikon :nama="$ikon" class="size-6" />
    </span>
    <h3 class="teks-h3">{{ $judul }}</h3>
    @if ($slot->isNotEmpty())
        <div class="max-w-md text-kecil text-cokelat-600">{{ $slot }}</div>
    @endif
    @isset($aksi)
        <div class="mt-2">{{ $aksi }}</div>
    @endisset
</div>

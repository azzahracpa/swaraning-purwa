@props(['nomor', 'label'])

{{-- Pembuka seksi: angka emas + belah ketupat + label kalimat biasa --}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="font-display text-angka-sedang font-medium text-emas-700">{{ $nomor }}</span>
    <span class="size-[7px] rotate-45 bg-emas" aria-hidden="true"></span>
    <span class="text-label font-semibold text-cokelat">{{ $label }}</span>
</div>

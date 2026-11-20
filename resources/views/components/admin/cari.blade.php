@props(['placeholder' => 'Cari', 'name' => 'cari'])

{{-- Kolom cari di halaman daftar admin. Form GET dikirim saat Enter atau otomatis setelah berhenti mengetik. --}}
<div class="relative w-full sm:max-w-[300px]" x-data>
    <label for="cari-{{ $name }}" class="sr-only">{{ $placeholder }}</label>
    <x-ikon nama="cari" class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
    <input id="cari-{{ $name }}" type="search" name="{{ $name }}" value="{{ request($name) }}"
           placeholder="{{ $placeholder }}" maxlength="100"
           x-on:input.debounce.600ms="$el.form.requestSubmit()"
           {{ $attributes->merge(['class' => 'kolom-input h-10 pl-10 text-kecil']) }}>
</div>

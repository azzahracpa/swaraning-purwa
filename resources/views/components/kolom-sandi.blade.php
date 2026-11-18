@props([
    'name',
    'label',
    'wajib' => false,
    'bantuan' => null,
    'id' => null,
])

{{--
    Kolom kata sandi dengan tombol tampilkan/sembunyikan (ikon mata).
    Pesan galat memakai @error seperti kolom isian lain.
--}}
@php
    $id = $id ?? $name;
    $galat = $errors->has($name);
@endphp

<div class="flex flex-col gap-2" x-data="{ terlihat: false }">
    <label for="{{ $id }}" class="kolom-label">
        {{ $label }}
        @if ($wajib)
            <span class="text-maroon" aria-hidden="true">*</span>
        @endif
    </label>

    <div class="relative">
        <input id="{{ $id }}" name="{{ $name }}" :type="terlihat ? 'text' : 'password'" type="password" @required($wajib)
               @if ($galat) aria-invalid="true" aria-describedby="{{ $id }}-galat" @endif
               {{ $attributes->merge(['class' => 'kolom-input pr-12'.($galat ? ' kolom-galat' : '')]) }}>
        <button type="button" x-on:click="terlihat = ! terlihat"
                class="absolute right-1.5 top-1/2 flex size-9 -translate-y-1/2 items-center justify-center rounded-kecil text-cokelat-600 transition hover:bg-lembut hover:text-cokelat"
                :aria-label="terlihat ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'" :aria-pressed="terlihat">
            <x-ikon nama="mata" class="size-5" x-bind:class="terlihat && 'text-maroon'" />
        </button>
    </div>

    @error($name)
        <p id="{{ $id }}-galat" class="kolom-pesan-galat">
            <x-ikon nama="peringatan" class="size-4" />
            {{ $message }}
        </p>
    @else
        @if ($bantuan)
            <p class="kolom-bantuan">{{ $bantuan }}</p>
        @endif
    @enderror
</div>

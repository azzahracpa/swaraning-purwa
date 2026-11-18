@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'wajib' => false,
    'bantuan' => null,
    'id' => null,
])

{{--
    Kolom isian lengkap: label, input, teks bantuan, dan pesan galat.
    Nilai lama otomatis diisi dengan old() kalau validasi gagal.
    Contoh: <x-kolom-isian name="email" label="Email" type="email" wajib />
            <x-kolom-isian name="title" label="Judul" :value="$article->title" />
--}}
@php
    $id = $id ?? $name;
    // name seperti "options[0][text]" diubah jadi "options.0.text" untuk old() dan @error
    $kunci = str_replace(['[', ']'], ['.', ''], $name);
    $galat = $errors->has($kunci);
    $nilai = $type === 'password' ? null : old($kunci, $value);
@endphp

<div class="flex flex-col gap-2">
    <label for="{{ $id }}" class="kolom-label">
        {{ $label }}
        @if ($wajib)
            <span class="text-maroon" aria-hidden="true">*</span>
        @endif
    </label>

    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" @required($wajib)
            @if ($galat) aria-invalid="true" aria-describedby="{{ $id }}-galat" @endif
            {{ $attributes->merge(['class' => 'kolom-input'.($galat ? ' kolom-galat' : ''), 'rows' => 5]) }}>{{ $nilai }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" @required($wajib)
            @if ($galat) aria-invalid="true" aria-describedby="{{ $id }}-galat" @endif
            {{ $attributes->merge(['class' => 'kolom-input'.($galat ? ' kolom-galat' : '')]) }}>
            {{ $slot }}
        </select>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $nilai }}" @required($wajib)
            @if ($galat) aria-invalid="true" aria-describedby="{{ $id }}-galat" @endif
            {{ $attributes->merge(['class' => 'kolom-input'.($galat ? ' kolom-galat' : '')]) }}>
    @endif

    @error($kunci)
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

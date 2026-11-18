@props(['value'])

<label {{ $attributes->merge(['class' => 'kolom-label']) }}>
    {{ $value ?? $slot }}
</label>

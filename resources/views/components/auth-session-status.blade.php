@props(['status'])

@if ($status)
    <x-peringatan jenis="sukses" {{ $attributes }}>{{ $status }}</x-peringatan>
@endif

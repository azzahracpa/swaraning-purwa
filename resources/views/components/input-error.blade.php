@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'flex flex-col gap-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="kolom-pesan-galat">
                <x-ikon nama="peringatan" class="size-4" />
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif

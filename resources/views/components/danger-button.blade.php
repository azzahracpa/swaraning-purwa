<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tombol tombol-bahaya']) }}>
    {{ $slot }}
</button>

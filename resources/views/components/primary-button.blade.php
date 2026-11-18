<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tombol tombol-utama']) }}>
    {{ $slot }}
</button>

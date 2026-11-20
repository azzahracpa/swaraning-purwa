@props(['href', 'aktif' => false, 'jumlah' => null])

{{-- Tombol filter berbentuk chip dengan angka jumlah, misal "Terbit 9" --}}
<a href="{{ $href }}" @if ($aktif) aria-current="true" @endif
   {{ $attributes->merge(['class' => 'inline-flex h-10 items-center gap-2 rounded-kecil border px-3.5 text-label font-semibold transition '.($aktif
        ? 'border-cokelat bg-cokelat text-ivory'
        : 'border-garis-kuat bg-putih text-cokelat hover:border-cokelat')]) }}>
    {{ $slot }}
    @if (! is_null($jumlah))
        <span class="rounded-full px-2 py-0.5 text-label-kecil {{ $aktif ? 'bg-emas text-cokelat' : 'bg-lembut text-cokelat-600' }}">{{ $jumlah }}</span>
    @endif
</a>

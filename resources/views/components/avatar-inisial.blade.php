@props(['user'])

{{-- Avatar berbasis inisial nama (tabel users tidak punya kolom foto) --}}
<span {{ $attributes->merge(['class' => 'inline-flex size-10 shrink-0 items-center justify-center rounded-full border border-emas bg-maroon-lembut text-label font-semibold text-maroon']) }}>
    {{ $user->inisial() }}
</span>

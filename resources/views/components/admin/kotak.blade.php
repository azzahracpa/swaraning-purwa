@props(['nomor' => null, 'label' => null])

{{-- Kotak putih untuk mengelompokkan isian formulir admin, dengan label seksi opsional --}}
<section {{ $attributes->merge(['class' => 'kartu flex flex-col gap-5 p-5 sm:p-6']) }}>
    @if ($label)
        <div class="flex items-center justify-between gap-3">
            <x-label-seksi :nomor="$nomor" :label="$label" />
            {{ $kanan ?? '' }}
        </div>
    @endif
    {{ $slot }}
</section>

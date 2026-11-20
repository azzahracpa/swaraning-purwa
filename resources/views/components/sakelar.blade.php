@props(['name', 'checked' => false, 'label' => null, 'labelMati' => null, 'disabled' => false])

{{--
    Sakelar aktif/nonaktif (checkbox bergaya toggle).
    Hidden input bernilai 0 dikirim lebih dulu, jadi saat sakelar mati nilainya tetap terkirim.
    Contoh: <x-sakelar name="is_available" :checked="old('is_available', true)" label="Dibuka untuk reservasi" />
--}}
<label {{ $attributes->merge(['class' => 'inline-flex select-none items-center gap-3 '.($disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer')]) }}
       x-data="{ nyala: @js((bool) $checked) }">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" x-model="nyala" @disabled($disabled)>
    <span class="relative h-6 w-11 shrink-0 rounded-full transition-colors duration-200 peer-focus-visible:ring-2 peer-focus-visible:ring-cokelat peer-focus-visible:ring-offset-2"
          :class="nyala ? 'bg-maroon' : 'bg-garis-kuat'" aria-hidden="true">
        <span class="absolute left-0.5 top-0.5 size-5 rounded-full bg-putih shadow transition-transform duration-200"
              :class="nyala && 'translate-x-5'"></span>
    </span>
    @if ($label)
        <span class="text-label font-semibold text-cokelat" x-text="nyala ? @js($label) : @js($labelMati ?? $label)">{{ $checked ? $label : ($labelMati ?? $label) }}</span>
    @endif
</label>

{{--
    Flash message pola POST → Redirect → GET.
    Controller cukup: return redirect()->route('...')->with('success', 'Pesan');
    atau ->with('error', 'Pesan galat').
--}}
@if (session('success') || session('error') || session('info'))
    <div {{ $attributes->merge(['class' => 'flex flex-col gap-3']) }}>
        @if (session('success'))
            <x-peringatan jenis="sukses" judul="Berhasil">{{ session('success') }}</x-peringatan>
        @endif
        @if (session('error'))
            <x-peringatan jenis="galat" judul="Gagal">{{ session('error') }}</x-peringatan>
        @endif
        @if (session('info'))
            <x-peringatan jenis="info" judul="Informasi">{{ session('info') }}</x-peringatan>
        @endif
    </div>
@endif

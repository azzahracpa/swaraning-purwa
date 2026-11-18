@extends('layouts.publik')

@section('title', 'Beranda')

@section('content')
    {{-- Versi awal beranda. Seksi lengkap (koleksi unggulan, artikel, reservasi) dikerjakan di modul Beranda. --}}
    <section class="wadah grid items-end gap-10 pb-24 pt-10 lg:grid-cols-[minmax(0,1fr)_420px] lg:gap-16 lg:pb-32 lg:pt-16">
        <div class="flex flex-col gap-8">
            <x-label-seksi nomor="01" label="Museum Mpu Purwa, Kota Malang" x-data x-muncul />

            <h1 class="teks-display-xl uppercase" x-data x-muncul="100">
                Swaraning <span class="aksen-miring">Purwa</span>
            </h1>

            <p class="teks-lead max-w-[560px] text-cokelat-600" x-data x-muncul="200">
                Media informasi dan edukasi digital untuk mengenal koleksi, sejarah, dan warisan budaya di Museum Mpu Purwa.
            </p>

            <div class="flex flex-wrap gap-3" x-data x-muncul="300">
                <a href="{{ \App\Support\Navigasi::url('koleksi.index') }}" class="tombol tombol-utama">
                    Jelajahi koleksi
                    <x-ikon nama="panah-kanan" />
                </a>
                <a href="{{ \App\Support\Navigasi::url('reservasi.index') }}" class="tombol tombol-garis">Ajukan jadwal kunjungan</a>
            </div>
        </div>

        <div x-data x-miring="5" class="w-full">
            @if ($profil?->gambar_url)
                <img src="{{ $profil->gambar_url }}" alt="Foto contoh {{ $profil->name }}"
                     class="aspect-[4/5] w-full rounded-relung object-cover object-left">
            @else
                <x-tempat-foto class="aspect-[4/5] w-full rounded-relung" keterangan="Halaman museum" />
            @endif
        </div>
    </section>
@endsection

@extends('layouts.publik')

@section('title', 'Profil Museum')

@php
    use App\Support\Navigasi;

    // Kolom history dipecah per paragraf (dipisah baris kosong)
    $paragrafSejarah = collect(preg_split('/\R\s*\R/', trim((string) $profil->history)))->filter()->values();
@endphp

@section('content')
    <section class="wadah pt-6">
        <x-breadcrumb :items="['Beranda' => route('beranda'), 'Profil Museum' => null]" />

        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-end lg:gap-16">
            <h1 class="teks-display-xl uppercase" x-data x-muncul>
                {{ \Illuminate\Support\Str::beforeLast($profil->name, ' ') }}
                <span class="aksen-miring block">{{ \Illuminate\Support\Str::afterLast($profil->name, ' ') }}</span>
            </h1>
            <div class="flex flex-col items-start gap-3 lg:pb-4" x-data x-muncul="120">
                <x-penanda-contoh teks="deskripsi menunggu verifikasi" />
                <p class="text-isi text-cokelat-600">{{ $profil->description }}</p>
            </div>
        </div>

        <div class="mt-10 h-[240px] overflow-hidden rounded-banner bg-cokelat sm:h-[360px] lg:h-[520px]" x-data x-muncul="200">
            @if ($profil->gambar_url)
                <img src="{{ $profil->gambar_url }}" alt="Foto contoh tampak depan museum"
                     class="size-full object-cover transition duration-700 hover:scale-[1.02]">
            @else
                <x-tempat-foto class="size-full rounded-none" keterangan="Foto tampak depan museum" />
            @endif
        </div>
    </section>

    {{-- 01 Sejarah --}}
    <section class="wadah grid gap-6 pt-20 lg:grid-cols-[304px_minmax(0,1fr)_200px] lg:gap-10 lg:pt-32">
        <x-label-seksi nomor="01" label="Sejarah" class="lg:pt-3" />

        <div class="flex flex-col gap-6" x-data x-muncul>
            <h2 class="teks-h1">Sejarah singkat</h2>
            @forelse ($paragrafSejarah as $paragraf)
                <p class="{{ $loop->first ? 'teks-lead text-cokelat' : 'text-isi text-cokelat-600' }}">{{ $paragraf }}</p>
            @empty
                <p class="text-isi text-cokelat-600">Sejarah museum belum diisi pengelola.</p>
            @endforelse
            <p class="flex flex-wrap items-center gap-2 text-kecil text-cokelat-600">
                <x-penanda-contoh teks="teks contoh" />
                Sumber: menunggu dokumen resmi museum
            </p>
        </div>

        <x-tempat-foto nada="lembut" class="h-[226px] w-full max-w-[220px] rounded-relung" keterangan="Foto arsip" x-data x-miring="6" />
    </section>

    {{-- 02 Informasi kunjungan --}}
    <section class="wadah pb-24 pt-20 lg:pb-32 lg:pt-32">
        <div class="grid gap-6 lg:grid-cols-[304px_minmax(0,1fr)] lg:items-end lg:gap-10">
            <x-label-seksi nomor="02" label="Informasi kunjungan" class="lg:pb-3" />
            <h2 class="teks-display-l uppercase" x-data x-muncul>Sebelum datang</h2>
        </div>

        @php
            $info = [
                ['jam', 'Jam buka', $profil->opening_hours ?? 'Jadwal buka per hari, diisi setelah verifikasi.'],
                ['lokasi', 'Alamat', $profil->address ?? 'Kota Malang. Alamat lengkap menyusul.'],
                ['telepon', 'Telepon', $profil->phone ?? 'Nomor resmi museum.'],
                ['surat', 'Email', $profil->email ?? 'Alamat email resmi museum.'],
            ];
        @endphp
        <div class="kartu mt-8 grid overflow-hidden sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($info as $i => [$ikon, $judul, $isi])
                <div class="flex flex-col gap-3 border-garis p-6 transition-colors hover:bg-ivory/60 max-lg:border-b sm:odd:border-r lg:border-r lg:last:border-r-0"
                     x-data x-muncul="{{ $i * 90 }}">
                    <x-ikon :nama="$ikon" class="text-maroon" />
                    <h3 class="teks-h3">{{ $judul }}</h3>
                    <p class="text-kecil text-cokelat-600">{{ $isi }}</p>
                    <x-penanda-contoh class="self-start" />
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex flex-col gap-5 sm:flex-row sm:items-center sm:gap-7">
            <a href="{{ Navigasi::url('reservasi.index') }}" class="tombol tombol-utama">Ajukan jadwal kunjungan</a>
            <a href="{{ Navigasi::url('koleksi.index') }}" class="tautan self-start sm:self-auto">Lihat katalog koleksi</a>
            <p class="text-kecil text-cokelat-600">Reservasi di situs ini berupa pengajuan dan ditinjau pengelola.</p>
        </div>
    </section>
@endsection

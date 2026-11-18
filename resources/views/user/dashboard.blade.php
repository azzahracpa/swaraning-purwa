@extends('layouts.user')

@section('title', 'Akun saya')

@php use App\Support\Navigasi; @endphp

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => null]" />
    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="teks-display-l uppercase" x-data x-muncul>
                Halo, <span class="aksen-miring">{{ $user->name }}</span>
            </h1>
            <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
                {{ now()->translatedFormat('l, j F Y') }}. Ringkasan reservasi, koleksi favorit, dan kuis Anda ada di sini.
            </p>
        </div>
        <a href="{{ Navigasi::url('reservasi.index') }}" class="tombol tombol-utama hidden self-start lg:inline-flex lg:self-auto">Ajukan reservasi baru</a>
    </div>
@endsection

@section('content')
    {{-- Kerangka dashboard. Data ringkasan diisi pada tahap Dashboard User. --}}
    <section class="flex flex-col gap-6" x-data x-muncul>
        <x-label-seksi nomor="01" label="Kunjungan berikutnya" />
        <div class="kartu flex flex-col gap-4 p-6 sm:p-8">
            <h2 class="teks-h2">Belum ada kunjungan terjadwal</h2>
            <p class="max-w-[520px] text-isi text-cokelat-600">
                Pilih jadwal yang tersedia, isi formulir, lalu tunggu keputusan pengelola. Statusnya muncul di sini.
            </p>
            <a href="{{ Navigasi::url('reservasi.index') }}" class="tombol tombol-utama self-start max-sm:w-full">Pilih jadwal kunjungan</a>
        </div>
    </section>

    <section class="mt-14 flex flex-col gap-6" x-data x-muncul="100">
        <x-label-seksi nomor="02" label="Ringkasan" />
        <div class="grid border-t border-cokelat md:grid-cols-3">
            @foreach ([
                ['Reservasi', 'Belum ada pengajuan reservasi. Riwayat dan statusnya akan tampil di sini.', 'Buka riwayat', 'reservasi.riwayat'],
                ['Koleksi favorit', 'Belum ada koleksi tersimpan. Tekan “Simpan ke favorit” di halaman detail koleksi.', 'Buka katalog koleksi', 'koleksi.index'],
                ['Kuis', 'Anda belum mengerjakan kuis. Skor terakhir akan tampil di sini.', 'Lihat daftar kuis', 'kuis.index'],
            ] as [$judul, $isi, $tautan, $rute])
                <div class="flex flex-col gap-3 border-b border-garis py-6 md:border-b-0 md:border-r md:px-6 md:first:pl-0 md:last:border-r-0">
                    <h3 class="teks-h3">{{ $judul }}</h3>
                    <p class="text-kecil text-cokelat-600">{{ $isi }}</p>
                    <a href="{{ Navigasi::url($rute) }}" class="tautan self-start">{{ $tautan }}</a>
                </div>
            @endforeach
        </div>
    </section>
@endsection

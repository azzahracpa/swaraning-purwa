@extends('layouts.admin')

@section('title', 'Dashboard Pengelola')
@section('halaman', 'Dashboard')

@section('content')
    {{-- Kerangka awal. Statistik dan reservasi terbaru dikerjakan di modul Dashboard Admin. --}}
    <div class="flex flex-col gap-2">
        <x-label-seksi nomor="01" label="Ringkasan" />
        <h1 class="teks-h1 mt-2">Selamat datang, {{ auth()->user()->name }}</h1>
        <p class="text-isi text-cokelat-600">Ruang pengelola konten dan reservasi Swaraning Purwa.</p>
    </div>

    <x-keadaan-kosong judul="Statistik sedang disiapkan" ikon="daftar" class="mt-10">
        Jumlah koleksi, artikel, reservasi menunggu, dan kuis aktif akan tampil di sini.
    </x-keadaan-kosong>
@endsection

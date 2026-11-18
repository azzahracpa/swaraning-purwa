@extends('layouts.user')

@section('title', 'Akun saya')

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => null]" />
    <h1 class="teks-display-l mt-6 uppercase">
        Halo, <span class="aksen-miring">{{ $user->name }}</span>
    </h1>
    <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
        {{ now()->translatedFormat('l, j F Y') }}. Ringkasan reservasi, koleksi favorit, dan kuis Anda ada di sini.
    </p>
@endsection

@section('content')
    {{-- Kerangka awal. Ringkasan lengkap dikerjakan di modul Dashboard User. --}}
    <x-label-seksi nomor="01" label="Ringkasan" />
    <x-keadaan-kosong judul="Ringkasan akun sedang disiapkan" ikon="info" class="mt-6">
        Reservasi, koleksi favorit, dan skor kuis akan tampil di sini.
    </x-keadaan-kosong>
@endsection

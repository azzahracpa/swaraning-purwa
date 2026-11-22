@extends('layouts.publik')

@section('title', 'Profil Museum')

@section('content')
    <section class="wadah pb-24 pt-6">
        <x-breadcrumb :items="['Beranda' => route('beranda'), 'Profil Museum' => null]" />
        <h1 class="teks-display-xl mt-8 uppercase">Museum <span class="aksen-miring">Mpu Purwa</span></h1>
        <x-keadaan-kosong judul="Profil museum belum diisi" ikon="lokasi" class="mt-10">
            Pengelola sedang menyiapkan sejarah, alamat, dan jam buka museum. Sementara itu, jelajahi koleksinya lebih dulu.
            <x-slot:aksi>
                <a href="{{ \App\Support\Navigasi::url('koleksi.index') }}" class="tombol tombol-utama tombol-kecil">Jelajahi koleksi</a>
            </x-slot:aksi>
        </x-keadaan-kosong>
    </section>
@endsection

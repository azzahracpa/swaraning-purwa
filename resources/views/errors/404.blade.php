@extends('errors.layout')

@section('kode', '404')
@section('label', 'Halaman tidak ditemukan')
@section('judul')
    Jejak ini <em class="aksen-miring">belum ditemukan</em>
@endsection
@section('pesan', 'Halaman yang Anda cari tidak ada, sudah dipindahkan, atau belum diterbitkan pengelola.')
@section('aksi')
    <a href="{{ url('/koleksi') }}" class="tombol tombol-utama">Jelajahi koleksi</a>
@endsection

@extends('errors.layout')

@section('kode', '403')
@section('label', 'Akses ditolak')
@section('judul')
    Ruang ini <em class="aksen-miring">tertutup</em> untuk Anda
@endsection
@section('pesan', $exception->getMessage() ?: 'Anda tidak memiliki hak untuk membuka halaman ini. Pastikan Anda masuk dengan akun yang benar.')
@section('aksi')
    @auth
        <a href="{{ auth()->user()->isAdmin() ? url('/admin') : url('/dashboard') }}" class="tombol tombol-utama">Ke halaman akun saya</a>
    @else
        <a href="{{ url('/login') }}" class="tombol tombol-utama">Masuk</a>
    @endauth
@endsection

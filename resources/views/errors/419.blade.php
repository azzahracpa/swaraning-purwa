@extends('errors.layout')

@section('kode', '419')
@section('label', 'Sesi berakhir')
@section('judul')
    Halaman sudah <em class="aksen-miring">kedaluwarsa</em>
@endsection
@section('pesan', 'Demi keamanan, formulir hanya berlaku sementara. Muat ulang halaman lalu kirim formulir sekali lagi.')
@section('aksi')
    <a href="{{ url()->previous() }}" class="tombol tombol-utama">Kembali dan coba lagi</a>
@endsection

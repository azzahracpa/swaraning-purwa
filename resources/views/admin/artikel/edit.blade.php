@extends('layouts.admin')

@section('title', 'Ubah Artikel')
@section('halaman', 'Kelola Artikel › Ubah')

@section('content')
    <form method="POST" action="{{ route('admin.artikel.update', $article) }}" enctype="multipart/form-data" id="form-artikel">
        @csrf
        @method('PUT')
        @include('admin.artikel._form', ['judulHalaman' => 'Ubah'])
    </form>

    {{-- Form hapus dipisah karena form tidak boleh bersarang --}}
    <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}" id="form-hapus-artikel" x-data
          x-on:submit.prevent="$store.konfirmasi.buka($el, {
              judul: 'Hapus artikel ini?',
              isi: 'Artikel dan gambar sampulnya akan dihapus permanen.',
              ringkasan: @js($article->title),
              tombol: 'Hapus permanen',
          })">
        @csrf
        @method('DELETE')
    </form>
@endsection

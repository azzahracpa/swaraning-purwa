@extends('layouts.admin')

@section('title', 'Tambah Koleksi')
@section('halaman', 'Kelola Koleksi › Tambah')

@section('content')
    <form method="POST" action="{{ route('admin.koleksi.store') }}" enctype="multipart/form-data"
          x-data="{ mengirim: false }" x-on:submit="mengirim = true">
        @csrf

        <x-admin.kepala judul="Tambah" aksen="koleksi" deskripsi="Kolom bertanda * wajib diisi. Foto pertama otomatis menjadi foto utama.">
            <a href="{{ route('admin.koleksi.index') }}" class="tombol tombol-garis">Batal</a>
            <button type="submit" class="tombol tombol-utama" :disabled="mengirim">Simpan koleksi</button>
        </x-admin.kepala>

        @if ($errors->any())
            <x-peringatan jenis="galat" judul="Koleksi belum disimpan" :tutup="false" class="mb-6">
                Periksa {{ $errors->count() }} isian yang ditandai, lalu simpan lagi.
            </x-peringatan>
        @endif

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            @include('admin.koleksi._form')

            <x-admin.kotak nomor="02" label="Foto koleksi">
                <x-admin.unggah-banyak name="foto" :maks="\App\Http\Requests\Admin\StoreKoleksiRequest::MAKS_FOTO" :sisa="\App\Http\Requests\Admin\StoreKoleksiRequest::MAKS_FOTO" />
            </x-admin.kotak>
        </div>
    </form>
@endsection

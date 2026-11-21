@extends('layouts.admin')

@section('title', 'Tulis Artikel')
@section('halaman', 'Kelola Artikel › Tulis')

@section('content')
    <form method="POST" action="{{ route('admin.artikel.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.artikel._form', ['judulHalaman' => 'Tulis'])
    </form>
@endsection

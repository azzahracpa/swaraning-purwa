@extends('layouts.user')

@section('title', 'Edit Profil')

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => \App\Support\Navigasi::url('dashboard'), 'Edit profil' => null]" />
    <h1 class="teks-display-l mt-6 uppercase">Edit <span class="aksen-miring">profil</span></h1>
@endsection

@section('content')
    <div class="flex flex-col gap-8">
        <div class="kartu p-6 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="kartu p-6 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="kartu p-6 sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection

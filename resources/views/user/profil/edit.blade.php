@extends('layouts.user')

@section('title', 'Edit Profil')

@php use App\Support\Navigasi; @endphp

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => Navigasi::url('dashboard'), 'Edit Profil' => null]" />
    <h1 class="teks-display-l mt-6 uppercase" x-data x-muncul>Edit <span class="aksen-miring">profil</span></h1>
    <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
        Perbarui data akun Anda. Nama dan telepon dipakai untuk mengisi otomatis formulir reservasi.
    </p>
@endsection

@section('content')
    <div class="grid gap-10 xl:grid-cols-[minmax(0,1fr)_240px]">
        <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-6"
              x-data="{ berubah: false, mengirim: false }"
              x-on:input="berubah = true"
              x-on:submit="mengirim = true">
            @csrf
            @method('PATCH')

            @if ($errors->any())
                <x-peringatan jenis="galat" judul="Perubahan belum disimpan" :tutup="false">
                    Perbaiki {{ $errors->count() }} isian yang ditandai, lalu simpan lagi.
                </x-peringatan>
            @endif

            <x-label-seksi nomor="01" label="Data akun" />
            <x-kolom-isian name="name" label="Nama lengkap" :value="$user->name" wajib autocomplete="name" />
            <x-kolom-isian name="email" label="Email" type="email" :value="$user->email" wajib autocomplete="email" />
            <x-kolom-isian name="phone" label="Nomor telepon" type="tel" :value="$user->phone"
                           placeholder="0812 3456 7890" bantuan="Opsional. Dipakai untuk mengisi formulir reservasi."
                           autocomplete="tel" />

            <x-label-seksi nomor="02" label="Ubah kata sandi" class="mt-4" />
            <p class="-mt-2 text-kecil text-cokelat-600">Kosongkan bagian ini bila tidak ingin mengganti kata sandi.</p>
            <x-kolom-sandi name="current_password" label="Kata sandi saat ini" autocomplete="current-password" />
            <x-kolom-sandi name="password" label="Kata sandi baru" bantuan="Minimal 8 karakter, kombinasi huruf dan angka."
                           autocomplete="new-password" />
            <x-kolom-sandi name="password_confirmation" label="Konfirmasi kata sandi baru" autocomplete="new-password" />

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit" class="tombol tombol-utama" :disabled="mengirim">
                    <span x-show="! mengirim">Simpan perubahan</span>
                    <span x-show="mengirim" x-cloak>Menyimpan…</span>
                </button>
                <a href="{{ route('profile.edit') }}" class="tombol tombol-garis">Batal</a>
                <p x-show="berubah && ! mengirim" x-cloak x-transition class="text-kecil text-status-menunggu-teks">
                    Ada perubahan yang belum disimpan.
                </p>
            </div>
        </form>

        {{-- Kartu ringkasan akun --}}
        <aside class="flex flex-col gap-3 self-start rounded-kartu bg-lembut p-5" x-data x-miring="4">
            <x-avatar-inisial :user="$user" class="size-12 text-h3-ponsel" />
            <p class="teks-h3">{{ $user->name }}</p>
            <p class="break-all text-kecil text-cokelat-600">{{ $user->email }}</p>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-kecil border border-garis-kuat bg-putih px-2.5 py-1 teks-label-kecil text-cokelat-600">
                    Peran: {{ $user->isAdmin() ? 'Pengelola' : 'Pengguna' }}
                </span>
                <x-penanda-contoh />
            </div>
            <div class="h-px bg-garis-kuat"></div>
            <p class="text-kecil text-cokelat-600">Bergabung sejak {{ $user->created_at->translatedFormat('j F Y') }}</p>
            <p class="text-kecil text-cokelat-600">Email dipakai untuk masuk. Kata sandi disimpan terenkripsi; pengelola tidak bisa melihatnya.</p>
        </aside>
    </div>
@endsection

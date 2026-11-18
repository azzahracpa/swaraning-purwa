<x-guest-layout>
    <h1 class="teks-h1">Daftar</h1>
    <p class="mt-4 text-isi text-cokelat-600">Buat akun untuk menyimpan favorit, mengajukan reservasi, dan mengerjakan kuis.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 flex flex-col gap-5">
        @csrf

        <x-kolom-isian name="name" label="Nama lengkap" wajib autofocus autocomplete="name" />
        <x-kolom-isian name="email" label="Email" type="email" placeholder="email@contoh.com" wajib autocomplete="username" />
        <x-kolom-isian name="password" label="Kata sandi" type="password" bantuan="Minimal 8 karakter." wajib autocomplete="new-password" />
        <x-kolom-isian name="password_confirmation" label="Konfirmasi kata sandi" type="password" wajib autocomplete="new-password" />

        <button type="submit" class="tombol tombol-utama w-full">Daftar</button>
    </form>

    <p class="mt-6 flex items-center gap-2 text-kecil text-cokelat-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="tautan">Masuk</a>
    </p>
</x-guest-layout>

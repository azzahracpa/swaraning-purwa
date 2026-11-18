<x-guest-layout>
    <h1 class="teks-h1">Masuk</h1>
    <p class="mt-4 text-isi text-cokelat-600">Gunakan email dan kata sandi akun Swaraning Purwa Anda.</p>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 flex flex-col gap-5">
        @csrf

        <x-kolom-isian name="email" label="Email" type="email" placeholder="email@contoh.com" wajib autofocus autocomplete="username" />
        <x-kolom-isian name="password" label="Kata sandi" type="password" placeholder="Masukkan kata sandi" wajib autocomplete="current-password" />

        <button type="submit" class="tombol tombol-utama w-full">Masuk</button>
    </form>

    <p class="mt-6 flex items-center gap-2 text-kecil text-cokelat-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="tautan">Daftar</a>
    </p>
</x-guest-layout>

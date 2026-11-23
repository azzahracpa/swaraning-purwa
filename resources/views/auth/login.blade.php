<x-guest-layout judul="Masuk">
    <h1 class="teks-h1">Masuk</h1>
    <p class="mt-4 text-isi text-cokelat-600">Gunakan email dan kata sandi akun Swaraning Purwa Anda.</p>

    @if ($errors->any())
        <x-peringatan jenis="galat" judul="Gagal masuk" :tutup="false" class="mt-6">
            Periksa kembali email dan kata sandi Anda.
        </x-peringatan>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 flex flex-col gap-5"
          x-data="{ mengirim: false }" x-on:submit="mengirim = true">
        @csrf

        <x-kolom-isian name="email" label="Email" type="email" placeholder="nama@email.com"
                       wajib autofocus autocomplete="username" />
        <x-kolom-sandi name="password" label="Kata sandi" placeholder="Masukkan kata sandi"
                       wajib autocomplete="current-password" />

        <button type="submit" class="tombol tombol-utama w-full" :disabled="mengirim">
            <span x-show="! mengirim">Masuk</span>
            <span x-show="mengirim" x-cloak>Memproses…</span>
        </button>
    </form>

    <p class="mt-6 flex items-center gap-2 text-kecil text-cokelat-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="tautan">Daftar</a>
    </p>
</x-guest-layout>

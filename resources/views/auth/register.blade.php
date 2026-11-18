<x-guest-layout judul="Daftar akun">
    <h1 class="teks-h1">Daftar akun</h1>
    <p class="mt-4 text-isi text-cokelat-600">Akun baru otomatis berperan sebagai pengguna. Isi data berikut.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 flex flex-col gap-5"
          x-data="{ sandi: '', mengirim: false }" x-on:submit="mengirim = true">
        @csrf

        <x-kolom-isian name="name" label="Nama lengkap" placeholder="Nama sesuai identitas"
                       wajib autofocus autocomplete="name" />
        <x-kolom-isian name="email" label="Email" type="email" placeholder="email@contoh.com"
                       wajib autocomplete="username" />

        <div class="flex flex-col gap-2">
            <x-kolom-sandi name="password" label="Kata sandi" placeholder="Minimal 8 karakter"
                           bantuan="Gunakan kombinasi huruf dan angka." wajib autocomplete="new-password"
                           x-model="sandi" />
            {{-- Indikator kekuatan kata sandi, hanya bantuan tampilan --}}
            <div class="flex gap-1.5" aria-hidden="true" x-show="sandi.length" x-cloak>
                <template x-for="i in 3" :key="i">
                    <span class="h-1 flex-1 rounded-full transition-colors duration-300"
                          :class="(sandi.length >= 8) + (/[a-zA-Z]/.test(sandi) && /[0-9]/.test(sandi)) + (sandi.length >= 12) >= i ? 'bg-status-disetujui-teks' : 'bg-garis'"></span>
                </template>
            </div>
        </div>

        <x-kolom-sandi name="password_confirmation" label="Konfirmasi kata sandi" placeholder="Ketik ulang kata sandi"
                       wajib autocomplete="new-password" />

        <button type="submit" class="tombol tombol-utama w-full" :disabled="mengirim">
            <span x-show="! mengirim">Buat akun</span>
            <span x-show="mengirim" x-cloak>Memproses…</span>
        </button>
    </form>

    <p class="mt-6 flex items-center gap-2 text-kecil text-cokelat-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="tautan">Masuk</a>
    </p>
</x-guest-layout>

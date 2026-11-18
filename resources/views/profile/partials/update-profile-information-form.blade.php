<section>
    <header>
        <h2 class="teks-h3">Informasi profil</h2>
        <p class="mt-1 text-kecil text-cokelat-600">Perbarui nama dan alamat email akun Anda.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 flex max-w-xl flex-col gap-5">
        @csrf
        @method('patch')

        <x-kolom-isian name="name" label="Nama" :value="$user->name" wajib autocomplete="name" />
        <x-kolom-isian name="email" label="Email" type="email" :value="$user->email" wajib autocomplete="username" />

        <div>
            <button type="submit" class="tombol tombol-utama">Simpan</button>
        </div>
    </form>
</section>

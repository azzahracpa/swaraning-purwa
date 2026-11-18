<section class="flex flex-col gap-6">
    <header>
        <h2 class="teks-h3">Hapus akun</h2>
        <p class="mt-1 max-w-xl text-kecil text-cokelat-600">
            Akun yang dihapus beserta reservasi, favorit, dan riwayat kuis tidak bisa dikembalikan.
        </p>
    </header>

    <div>
        <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            Hapus akun
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" maxWidth="lg" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="flex flex-col gap-5 p-6 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="teks-h3">Yakin ingin menghapus akun?</h2>
            <p class="text-isi text-cokelat-600">Masukkan kata sandi untuk mengonfirmasi penghapusan akun secara permanen.</p>

            <div class="flex flex-col gap-2">
                <x-input-label for="password" value="Kata sandi" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="Kata sandi" />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-danger-button>Hapus permanen</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

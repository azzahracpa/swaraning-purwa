<section>
    <header>
        <h2 class="teks-h3">Ubah kata sandi</h2>
        <p class="mt-1 text-kecil text-cokelat-600">Gunakan kata sandi yang panjang dan sulit ditebak.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 flex max-w-xl flex-col gap-5">
        @csrf
        @method('put')

        <div class="flex flex-col gap-2">
            <x-input-label for="update_password_current_password" value="Kata sandi saat ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="flex flex-col gap-2">
            <x-input-label for="update_password_password" value="Kata sandi baru" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="flex flex-col gap-2">
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi kata sandi baru" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div>
            <x-primary-button>Simpan kata sandi</x-primary-button>
        </div>
    </form>
</section>

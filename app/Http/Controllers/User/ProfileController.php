<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Form edit profil (nama, email, telepon, dan ganti kata sandi).
     */
    public function edit(Request $request): View
    {
        return view('user.profil.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Simpan perubahan profil. Kata sandi hanya diganti kalau kolomnya diisi.
     */
    public function update(UpdateProfilRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (! empty($data['password'])) {
            // Cast "hashed" di model User otomatis meng-hash kata sandi
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}

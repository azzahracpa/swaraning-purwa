<?php

namespace Tests\Feature;

use App\Models\MuseumProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfilMuseumTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bisa_mengubah_profil_museum_dan_gambarnya(): void
    {
        Storage::fake();
        $admin = User::factory()->admin()->create();
        MuseumProfile::factory()->create();

        $this->actingAs($admin)->get(route('admin.profil-museum.edit'))->assertOk()->assertSee('Pratinjau di situs');

        $this->actingAs($admin)->put(route('admin.profil-museum.update'), [
            'name' => 'Museum Mpu Purwa',
            'address' => 'Jl. Contoh No. 1',
            'email' => 'museum@contoh.test',
            'opening_hours' => 'Selasa–Minggu',
            'image' => UploadedFile::fake()->image('gedung.png'),
        ])->assertRedirect(route('admin.profil-museum.edit'))->assertSessionHas('success');

        $profil = MuseumProfile::utama();
        $this->assertSame('Jl. Contoh No. 1', $profil->address);
        Storage::assertExists($profil->image_url);
        $this->assertSame(1, MuseumProfile::count());
    }

    public function test_email_tidak_valid_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        MuseumProfile::factory()->create();

        $this->actingAs($admin)->put(route('admin.profil-museum.update'), [
            'name' => 'Museum Mpu Purwa',
            'email' => 'museum.mpupurwa@',
        ])->assertSessionHasErrors(['email' => 'Format email belum benar. Contoh: nama@domain.id']);
    }

    public function test_user_biasa_ditolak(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.profil-museum.edit'))->assertForbidden();
    }
}

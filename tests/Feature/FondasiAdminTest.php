<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Gambar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FondasiAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_layout_admin_menampilkan_footer_pengelola(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Ruang pengelola')
            ->assertSee('Museum Mpu Purwa, Kota Malang');
    }

    public function test_helper_gambar_menyimpan_ke_disk_default(): void
    {
        Storage::fake();

        $path = Gambar::simpan(UploadedFile::fake()->image('arca.jpg'), 'koleksi');

        $this->assertStringStartsWith('koleksi/', $path);
        Storage::assertExists($path);

        Gambar::hapus($path);
        Storage::assertMissing($path);
    }

    public function test_helper_gambar_tidak_menghapus_gambar_contoh(): void
    {
        Gambar::hapus('images/museum/arca-ganesha.jpg');

        $this->assertFileExists(public_path('images/museum/arca-ganesha.jpg'));
    }
}

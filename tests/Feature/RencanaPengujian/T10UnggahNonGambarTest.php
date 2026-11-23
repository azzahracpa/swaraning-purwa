<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Article;
use App\Models\Category;
use App\Models\Collection;
use App\Models\MuseumProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * T10 · Unggah file non-gambar
 * Hasil yang diharapkan: ditolak, di semua form yang menerima gambar.
 */
class T10UnggahNonGambarTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Contoh berkas yang bukan gambar, termasuk yang menyamar sebagai .jpg.
     *
     * @return array<string, UploadedFile>
     */
    private function berkasPalsu(): array
    {
        return [
            'pdf' => UploadedFile::fake()->create('dokumen.pdf', 120, 'application/pdf'),
            'exe' => UploadedFile::fake()->create('program.exe', 50, 'application/octet-stream'),
            'teks bernama jpg' => $this->berkasTeksBernamaJpg(),
            'svg' => UploadedFile::fake()->createWithContent('ikon.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
        ];
    }

    /**
     * Berkas teks sungguhan yang diberi nama .jpg. Tidak memakai UploadedFile::fake()
     * karena berkas palsu melaporkan tipe MIME dari nama berkas, bukan dari isinya.
     */
    private function berkasTeksBernamaJpg(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'palsu');
        file_put_contents($path, 'ini teks biasa, bukan gambar');

        return new UploadedFile($path, 'foto.jpg', null, null, true);
    }

    public function test_sampul_artikel_non_gambar_ditolak(): void
    {
        foreach ($this->berkasPalsu() as $jenis => $berkas) {
            $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
                'title' => 'Artikel '.$jenis,
                'content' => 'Isi artikel contoh.',
                'status' => 'draf',
                'image' => $berkas,
            ])->assertSessionHasErrors('image');
        }

        $this->assertSame(0, Article::count());
    }

    public function test_foto_profil_museum_non_gambar_ditolak(): void
    {
        $profil = MuseumProfile::factory()->create();

        foreach ($this->berkasPalsu() as $berkas) {
            $this->actingAs($this->admin)->put(route('admin.profil-museum.update'), [
                'name' => 'Museum Mpu Purwa',
                'address' => 'Jl. Contoh',
                'image' => $berkas,
            ])->assertSessionHasErrors('image');
        }

        $this->assertSame($profil->image_url, $profil->refresh()->image_url);
    }

    public function test_foto_koleksi_non_gambar_ditolak_saat_tambah_dan_unggah(): void
    {
        $kategori = Category::factory()->create();
        $koleksi = Collection::factory()->for($kategori)->create();

        foreach ($this->berkasPalsu() as $berkas) {
            $this->actingAs($this->admin)->post(route('admin.koleksi.store'), [
                'category_id' => $kategori->id,
                'name' => 'Koleksi baru',
                'description' => 'Deskripsi contoh.',
                'foto' => [$berkas],
            ])->assertSessionHasErrors('foto.0');

            $this->actingAs($this->admin)->post(route('admin.koleksi.foto.store', $koleksi), ['foto' => [$berkas]])
                ->assertSessionHasErrors('foto.0');
        }

        $this->assertDatabaseMissing('collections', ['name' => 'Koleksi baru']);
        $this->assertSame(0, $koleksi->images()->count());
    }

    public function test_tidak_ada_berkas_tersimpan_di_storage(): void
    {
        $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
            'title' => 'Artikel', 'content' => 'Isi.', 'status' => 'draf',
            'image' => UploadedFile::fake()->create('dokumen.pdf', 120, 'application/pdf'),
        ]);

        $this->assertEmpty(Storage::allFiles());
    }

    public function test_gambar_asli_tetap_diterima(): void
    {
        $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
            'title' => 'Artikel bergambar', 'content' => 'Isi.', 'status' => 'draf',
            'image' => UploadedFile::fake()->image('sampul.webp', 1200, 800),
        ])->assertSessionHasNoErrors();

        $this->assertCount(1, Storage::allFiles('artikel'));
    }
}

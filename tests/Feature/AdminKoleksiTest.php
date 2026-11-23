<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminKoleksiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        Storage::fake();
    }

    private function dataKoleksi(array $tambahan = []): array
    {
        return array_merge([
            'category_id' => Category::factory()->create()->id,
            'name' => 'Arca Ganesha',
            'description' => 'Arca duduk dengan perut buncit.',
            'material' => 'Batu andesit',
        ], $tambahan);
    }

    public function test_tambah_koleksi_dengan_foto_dan_foto_pertama_jadi_utama(): void
    {
        $this->actingAs($this->admin)->post(route('admin.koleksi.store'), $this->dataKoleksi([
            'foto' => [UploadedFile::fake()->image('depan.jpg', 800, 600), UploadedFile::fake()->image('samping.png', 640, 480)],
        ]))->assertSessionHasNoErrors();

        $koleksi = Collection::where('name', 'Arca Ganesha')->firstOrFail();
        $this->assertSame(2, $koleksi->images()->count());
        $this->assertSame(1, $koleksi->images()->where('is_primary', true)->count());
        $koleksi->images->each(fn ($foto) => Storage::assertExists($foto->image_url));
    }

    public function test_unggahan_bukan_gambar_ditolak(): void
    {
        $this->actingAs($this->admin)->post(route('admin.koleksi.store'), $this->dataKoleksi([
            'foto' => [UploadedFile::fake()->create('virus.exe', 50, 'application/octet-stream')],
        ]))->assertSessionHasErrors('foto.0');

        $this->assertDatabaseMissing('collections', ['name' => 'Arca Ganesha']);
    }

    public function test_foto_lebih_dari_2mb_ditolak(): void
    {
        $koleksi = Collection::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.koleksi.foto.store', $koleksi), [
            'foto' => [UploadedFile::fake()->image('besar.jpg', 800, 600)->size(3400)],
        ])->assertSessionHasErrors('foto.0');
    }

    public function test_deskripsi_wajib_diisi(): void
    {
        $this->actingAs($this->admin)->post(route('admin.koleksi.store'), $this->dataKoleksi(['description' => '']))
            ->assertSessionHasErrors(['description' => 'Deskripsi wajib diisi agar halaman detail koleksi tidak kosong.']);
    }

    public function test_ganti_foto_utama_hanya_satu(): void
    {
        $koleksi = Collection::factory()->create();
        $pertama = CollectionImage::factory()->for($koleksi)->create(['is_primary' => true]);
        $kedua = CollectionImage::factory()->for($koleksi)->create();

        $this->actingAs($this->admin)->patch(route('admin.koleksi.foto.update', [$koleksi, $kedua]), ['utama' => 1])
            ->assertRedirect(route('admin.koleksi.edit', $koleksi));

        $this->assertFalse($pertama->refresh()->is_primary);
        $this->assertTrue($kedua->refresh()->is_primary);
    }

    public function test_hapus_foto_utama_memindahkan_status_utama(): void
    {
        $koleksi = Collection::factory()->create();
        $utama = CollectionImage::factory()->for($koleksi)->create(['is_primary' => true]);
        $lain = CollectionImage::factory()->for($koleksi)->create();

        $this->actingAs($this->admin)->delete(route('admin.koleksi.foto.destroy', [$koleksi, $utama]));

        $this->assertModelMissing($utama);
        $this->assertTrue($lain->refresh()->is_primary);
    }

    public function test_foto_milik_koleksi_lain_tidak_bisa_diubah_lewat_url(): void
    {
        $koleksiA = Collection::factory()->create();
        $fotoB = CollectionImage::factory()->for(Collection::factory())->create();

        $this->actingAs($this->admin)->delete(route('admin.koleksi.foto.destroy', [$koleksiA, $fotoB]))->assertNotFound();
        $this->assertModelExists($fotoB);
    }

    public function test_aksi_massal_pindah_kategori_dan_hapus(): void
    {
        $tujuan = Category::factory()->create();
        [$a, $b, $c] = Collection::factory()->count(3)->create();

        $this->actingAs($this->admin)->post(route('admin.koleksi.aksi-massal'), [
            'aksi' => 'kategori', 'pilih' => [$a->id, $b->id], 'category_id' => $tujuan->id,
        ])->assertSessionHas('success');
        $this->assertSame($tujuan->id, $a->refresh()->category_id);

        $this->actingAs($this->admin)->post(route('admin.koleksi.aksi-massal'), [
            'aksi' => 'hapus', 'pilih' => [$a->id, $b->id],
        ])->assertSessionHas('success');
        $this->assertModelMissing($a);
        $this->assertModelExists($c);
    }

    public function test_daftar_dan_halaman_ubah_bisa_dibuka(): void
    {
        $koleksi = Collection::factory()->create(['name' => 'Yoni']);
        CollectionImage::factory()->for($koleksi)->create(['is_primary' => true]);

        $this->actingAs($this->admin)->get(route('admin.koleksi.index'))->assertOk()->assertSee('Yoni');
        $this->actingAs($this->admin)->get(route('admin.koleksi.edit', $koleksi))->assertOk()->assertSee('Foto utama');
        $this->actingAs($this->admin)->get(route('admin.koleksi.create'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.kategori.index'))->assertOk();
    }

    public function test_berkas_bukan_gambar_yang_diganti_ekstensinya_ditolak(): void
    {
        $koleksi = Collection::factory()->create();

        // Nama .jpg tetapi isinya teks biasa
        $palsu = UploadedFile::fake()->createWithContent('arca.jpg', 'ini bukan gambar');

        $this->actingAs($this->admin)->post(route('admin.koleksi.foto.store', $koleksi), ['foto' => [$palsu]])
            ->assertSessionHasErrors('foto.0');
        $this->assertSame(0, $koleksi->images()->count());
    }

    public function test_foto_terlalu_kecil_ditolak_dengan_pesan_jelas(): void
    {
        $koleksi = Collection::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.koleksi.foto.store', $koleksi), [
            'foto' => [UploadedFile::fake()->image('kecil.png', 200, 150)],
        ])->assertSessionHasErrors(['foto.0' => 'Ukuran foto minimal 400 × 400 piksel.']);
    }
}

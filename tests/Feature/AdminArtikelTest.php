<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminArtikelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        Storage::fake();
    }

    public function test_daftar_artikel_admin_bisa_dibuka_dan_difilter(): void
    {
        Article::factory()->create(['title' => 'Artikel Terbit']);
        Article::factory()->draf()->create(['title' => 'Artikel Draf']);

        $this->actingAs($this->admin)->get(route('admin.artikel.index'))
            ->assertOk()->assertSee('Artikel Terbit')->assertSee('Artikel Draf');

        $this->actingAs($this->admin)->get(route('admin.artikel.index', ['status' => 'draf']))
            ->assertOk()->assertSee('Artikel Draf')->assertDontSee('Artikel Terbit');
    }

    public function test_user_biasa_tidak_bisa_membuka_kelola_artikel(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.artikel.index'))->assertForbidden();
    }

    public function test_admin_bisa_menerbitkan_artikel_dengan_gambar(): void
    {
        $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
            'title' => 'Artikel Baru',
            'content' => "Paragraf satu.\n\n## Subjudul\n\nParagraf dua.",
            'status' => 'terbit',
            'image' => UploadedFile::fake()->image('sampul.jpg', 1200, 800),
        ])->assertRedirect(route('admin.artikel.index'))->assertSessionHas('success');

        $artikel = Article::where('title', 'Artikel Baru')->first();
        $this->assertSame($this->admin->id, $artikel->author_id);
        $this->assertTrue($artikel->sudahTerbit());
        Storage::assertExists($artikel->image_url);
    }

    public function test_artikel_draf_tidak_punya_tanggal_terbit(): void
    {
        $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
            'title' => 'Masih Draf',
            'content' => 'Isi.',
            'status' => 'draf',
        ]);

        $this->assertNull(Article::where('title', 'Masih Draf')->value('published_at'));
    }

    public function test_jadwal_terbit_di_masa_lalu_ditolak(): void
    {
        $this->actingAs($this->admin)->from(route('admin.artikel.create'))->post(route('admin.artikel.store'), [
            'title' => 'Terjadwal',
            'content' => 'Isi.',
            'status' => 'jadwal',
            'tanggal_terbit' => now()->subDay()->format('Y-m-d'),
            'jam_terbit' => '09:00',
        ])->assertSessionHasErrors('tanggal_terbit');

        $this->assertDatabaseMissing('articles', ['title' => 'Terjadwal']);
    }

    public function test_artikel_terjadwal_tersimpan_dengan_tanggal_masa_depan(): void
    {
        $besok = now()->addDays(2);

        $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
            'title' => 'Terjadwal',
            'content' => 'Isi.',
            'status' => 'jadwal',
            'tanggal_terbit' => $besok->format('Y-m-d'),
            'jam_terbit' => '10:30',
        ])->assertSessionHasNoErrors();

        $artikel = Article::where('title', 'Terjadwal')->first();
        $this->assertSame('terjadwal', $artikel->status());
        $this->assertSame($besok->format('Y-m-d').' 10:30', $artikel->published_at->format('Y-m-d H:i'));
    }

    public function test_unggahan_bukan_gambar_ditolak(): void
    {
        $this->actingAs($this->admin)->post(route('admin.artikel.store'), [
            'title' => 'Dengan PDF',
            'content' => 'Isi.',
            'status' => 'draf',
            'image' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('image');
    }

    public function test_ubah_dan_hapus_gambar_artikel(): void
    {
        $artikel = Article::factory()->create(['image_url' => UploadedFile::fake()->image('lama.jpg')->store('artikel')]);
        $lama = $artikel->image_url;

        $this->actingAs($this->admin)->put(route('admin.artikel.update', $artikel), [
            'title' => 'Judul Diubah',
            'content' => 'Isi baru.',
            'status' => 'terbit',
            'hapus_image' => 1,
        ])->assertRedirect(route('admin.artikel.index'));

        $artikel->refresh();
        $this->assertSame('Judul Diubah', $artikel->title);
        $this->assertNull($artikel->image_url);
        Storage::assertMissing($lama);
    }

    public function test_hapus_artikel_menghapus_gambar(): void
    {
        $artikel = Article::factory()->create(['image_url' => UploadedFile::fake()->image('a.jpg')->store('artikel')]);
        $path = $artikel->image_url;

        $this->actingAs($this->admin)->delete(route('admin.artikel.destroy', $artikel))
            ->assertRedirect(route('admin.artikel.index'));

        $this->assertModelMissing($artikel);
        Storage::assertMissing($path);
    }
}

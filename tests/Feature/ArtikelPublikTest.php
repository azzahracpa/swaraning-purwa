<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtikelPublikTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_artikel_hanya_menampilkan_yang_terbit(): void
    {
        $terbit = Article::factory()->create(['title' => 'Artikel yang sudah terbit']);
        $draf = Article::factory()->draf()->create(['title' => 'Artikel masih draf']);
        $terjadwal = Article::factory()->create([
            'title' => 'Artikel terjadwal besok',
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('artikel.index'))
            ->assertOk()
            ->assertSee($terbit->title)
            ->assertDontSee($draf->title)
            ->assertDontSee($terjadwal->title);
    }

    public function test_daftar_artikel_memakai_paginasi(): void
    {
        Article::factory()->count(8)->create();

        $this->get(route('artikel.index'))->assertOk()->assertSee('Navigasi halaman', false);
        $this->get(route('artikel.index', ['page' => 2]))->assertOk();
    }

    public function test_halaman_baca_menampilkan_isi_dan_subjudul(): void
    {
        $artikel = Article::factory()->create([
            'content' => "Paragraf pembuka.\n\n## Subjudul bagian\n\nParagraf berikutnya.",
        ]);

        $this->get(route('artikel.show', $artikel))
            ->assertOk()
            ->assertSee($artikel->title)
            ->assertSee('<h2 class="teks-h2 mt-4">Subjudul bagian</h2>', false)
            ->assertSee('Paragraf berikutnya.');
    }

    public function test_artikel_draf_tidak_bisa_dibuka_publik(): void
    {
        $draf = Article::factory()->draf()->create();

        $this->get(route('artikel.show', $draf))->assertNotFound();
    }

    public function test_isi_artikel_di_escape(): void
    {
        $artikel = Article::factory()->create(['content' => '<script>alert(1)</script>']);

        $this->get(route('artikel.show', $artikel))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}

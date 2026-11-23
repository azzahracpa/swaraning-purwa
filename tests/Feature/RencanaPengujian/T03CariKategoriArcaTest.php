<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T03 · Cari kategori Arca
 * Hasil yang diharapkan: hanya koleksi terkait yang tampil.
 */
class T03CariKategoriArcaTest extends TestCase
{
    use RefreshDatabase;

    private Category $arca;

    protected function setUp(): void
    {
        parent::setUp();
        $this->arca = Category::factory()->create(['name' => 'Arca']);
        $prasasti = Category::factory()->create(['name' => 'Prasasti']);

        Collection::factory()->for($this->arca)->create(['name' => 'Arca Ganesha']);
        Collection::factory()->for($this->arca)->create(['name' => 'Arca Nandi']);
        Collection::factory()->for($prasasti)->create(['name' => 'Prasasti Dinoyo']);
        // Nama mengandung "arca" tetapi kategorinya bukan Arca
        Collection::factory()->for($prasasti)->create(['name' => 'Prasasti tentang arca']);
    }

    public function test_filter_kategori_arca_hanya_menampilkan_koleksi_arca(): void
    {
        $respons = $this->get(route('koleksi.index', ['kategori' => $this->arca->id]))->assertOk();

        $respons->assertSee('Arca Ganesha')->assertSee('Arca Nandi')
            ->assertDontSee('Prasasti Dinoyo')->assertDontSee('Prasasti tentang arca');
        $this->assertSame(2, $respons->viewData('koleksi')->total());
        $this->assertTrue($respons->viewData('koleksi')->every(fn ($k) => $k->category_id === $this->arca->id));
    }

    public function test_kata_kunci_digabung_dengan_kategori(): void
    {
        $this->get(route('koleksi.index', ['kategori' => $this->arca->id, 'cari' => 'nandi']))
            ->assertOk()->assertSee('Arca Nandi')->assertDontSee('Arca Ganesha');
    }

    public function test_kategori_ikut_terbawa_di_tautan_halaman_berikutnya(): void
    {
        Collection::factory()->count(8)->for($this->arca)->create();

        $respons = $this->get(route('koleksi.index', ['kategori' => $this->arca->id]))->assertOk();

        $this->assertStringContainsString('kategori='.$this->arca->id, $respons->viewData('koleksi')->nextPageUrl());
    }

    public function test_kategori_yang_tidak_ada_ditolak_dengan_pesan(): void
    {
        $this->get(route('koleksi.index', ['kategori' => 9999]))
            ->assertSessionHasErrors(['kategori' => 'Kategori yang dipilih tidak ditemukan.']);
    }
}

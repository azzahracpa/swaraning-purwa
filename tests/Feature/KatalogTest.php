<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_katalog_menampilkan_koleksi(): void
    {
        $koleksi = Collection::factory()->create(['name' => 'Arca Ganesha']);

        $this->get(route('koleksi.index'))
            ->assertOk()
            ->assertSee('Arca Ganesha')
            ->assertSee($koleksi->category->name);
    }

    public function test_pencarian_berdasarkan_nama(): void
    {
        Collection::factory()->create(['name' => 'Arca Ganesha']);
        Collection::factory()->create(['name' => 'Kendi Terakota']);

        $this->get(route('koleksi.index', ['cari' => 'ganesha']))
            ->assertOk()
            ->assertSee('Arca Ganesha')
            ->assertDontSee('Kendi Terakota');
    }

    public function test_filter_kategori_hanya_menampilkan_koleksi_terkait(): void
    {
        $arca = Category::factory()->create(['name' => 'Arca']);
        $prasasti = Category::factory()->create(['name' => 'Prasasti']);
        Collection::factory()->for($arca)->create(['name' => 'Arca Dwarapala']);
        Collection::factory()->for($prasasti)->create(['name' => 'Prasasti Batu']);

        $this->get(route('koleksi.index', ['kategori' => $arca->id]))
            ->assertOk()
            ->assertSee('Arca Dwarapala')
            ->assertDontSee('Prasasti Batu');
    }

    public function test_hasil_kosong_menampilkan_pesan(): void
    {
        Collection::factory()->create(['name' => 'Arca Ganesha']);

        $this->get(route('koleksi.index', ['cari' => 'arca singa']))
            ->assertOk()
            ->assertSee('Koleksi tidak ditemukan');
    }

    public function test_detail_koleksi_menampilkan_galeri_dan_keterangan(): void
    {
        $koleksi = Collection::factory()->create(['material' => 'Batu andesit']);
        CollectionImage::factory()->for($koleksi)->create(['is_primary' => true, 'caption' => 'Tampak depan']);
        CollectionImage::factory()->for($koleksi)->create(['caption' => 'Tampak samping']);

        $this->get(route('koleksi.show', $koleksi))
            ->assertOk()
            ->assertSee($koleksi->name)
            ->assertSee('Batu andesit')
            ->assertSee('Tampak samping')
            ->assertSee('Masuk untuk menyimpan favorit');
    }

    public function test_detail_koleksi_tidak_ada_mengembalikan_404(): void
    {
        $this->get('/koleksi/99999')->assertNotFound();
    }
}

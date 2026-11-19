<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Collection;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_menampilkan_koleksi_dan_artikel_terbaru(): void
    {
        $this->seed(DatabaseSeeder::class);

        $koleksi = Collection::latest()->first();
        $artikel = Article::terbit()->latest('published_at')->first();

        $this->get('/')
            ->assertOk()
            ->assertSee('Jejak yang')
            ->assertSee($koleksi->name)
            ->assertSee($artikel->title)
            ->assertSee('Rencanakan kunjungan Anda');
    }

    public function test_beranda_tetap_tampil_saat_data_kosong(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Koleksi belum tersedia')
            ->assertSee('Belum ada artikel terbit');
    }

    public function test_artikel_draf_tidak_tampil_di_beranda(): void
    {
        $draf = Article::factory()->draf()->create(['title' => 'Draf rahasia pengelola']);

        $this->get('/')->assertOk()->assertDontSee($draf->title);
    }
}

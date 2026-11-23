<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\MuseumProfile;
use Database\Seeders\ArticleSeeder;
use Database\Seeders\MuseumProfileSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederArtikelTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_artikel_dan_profil_aman_dijalankan_ulang(): void
    {
        $this->seed([UserSeeder::class, MuseumProfileSeeder::class, ArticleSeeder::class]);
        $this->seed([MuseumProfileSeeder::class, ArticleSeeder::class]);

        $this->assertSame(8, Article::count());
        $this->assertSame(1, MuseumProfile::count());
    }

    public function test_profil_yang_sudah_diisi_pengelola_tidak_ditimpa(): void
    {
        MuseumProfile::factory()->create(['address' => 'Alamat dari pengelola']);

        $this->seed(MuseumProfileSeeder::class);

        $this->assertSame(1, MuseumProfile::count());
        $this->assertSame('Alamat dari pengelola', MuseumProfile::utama()->address);
    }

    public function test_artikel_contoh_berlabel_dan_gambarnya_ada(): void
    {
        $this->seed([UserSeeder::class, ArticleSeeder::class]);

        foreach (Article::all() as $artikel) {
            $this->assertStringContainsString('contoh', $artikel->title);
            $this->assertFileExists(public_path($artikel->image_url));
        }
        $this->assertSame(1, Article::whereNull('published_at')->count(), 'Satu artikel draf untuk contoh tampilan admin.');
    }

    public function test_favicon_bawaan_laravel_sudah_diganti(): void
    {
        $this->assertFileDoesNotExist(public_path('favicon.ico'));
        $this->assertFileExists(public_path('images/ui/tanda.svg'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionImage;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CollectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederKoleksiTest extends TestCase
{
    use RefreshDatabase;

    private function seedKoleksi(): void
    {
        $this->seed([CategorySeeder::class, CollectionSeeder::class]);
    }

    public function test_seeder_koleksi_aman_dijalankan_ulang(): void
    {
        $this->seedKoleksi();
        $jumlah = [Category::count(), Collection::count(), CollectionImage::count()];

        $this->seedKoleksi();

        $this->assertSame($jumlah, [Category::count(), Collection::count(), CollectionImage::count()]);
    }

    public function test_setiap_koleksi_punya_tepat_satu_foto_utama_yang_ada_berkasnya(): void
    {
        $this->seedKoleksi();

        foreach (Collection::with('images')->get() as $koleksi) {
            $this->assertSame(1, $koleksi->images->where('is_primary', true)->count(), $koleksi->name);
            foreach ($koleksi->images as $foto) {
                $this->assertFileExists(public_path($foto->image_url));
            }
        }
    }

    public function test_suntingan_pengelola_tidak_ditimpa(): void
    {
        $this->seedKoleksi();
        Collection::where('name', 'Arca Ganesha')->update(['location' => 'Ruang baru']);

        $this->seedKoleksi();

        $this->assertSame('Ruang baru', Collection::where('name', 'Arca Ganesha')->value('location'));
    }
}

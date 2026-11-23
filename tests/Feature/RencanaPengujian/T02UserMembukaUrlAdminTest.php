<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * T02 · User membuka URL admin
 * Hasil yang diharapkan: akses ditolak.
 */
class T02UserMembukaUrlAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Semua route GET berawalan "admin." dengan parameter diisi data contoh.
     *
     * @return array<string, string>
     */
    private function semuaUrlAdmin(): array
    {
        $isi = [
            'article' => Article::factory()->create(),
            'collection' => Collection::factory()->create(),
            'reservation' => Reservation::factory()->create(),
        ];

        $url = [];
        foreach (Route::getRoutes() as $route) {
            $nama = $route->getName();
            if (! $nama || ! str_starts_with($nama, 'admin.') || ! in_array('GET', $route->methods())) {
                continue;
            }
            $parameter = [];
            foreach ($route->parameterNames() as $p) {
                $model = $isi[$p] ?? null;
                if (! $model) {
                    continue 2; // parameter yang tidak disiapkan dilewati
                }
                $parameter[$p] = $model;
            }
            $url[$nama] = route($nama, $parameter);
        }

        return $url;
    }

    public function test_user_biasa_ditolak_di_semua_halaman_admin(): void
    {
        $user = User::factory()->create();
        $url = $this->semuaUrlAdmin();

        $this->assertGreaterThan(10, count($url), 'Daftar route admin terlalu sedikit, cek pola nama route.');
        foreach ($url as $nama => $alamat) {
            $this->actingAs($user)->get($alamat)->assertForbidden();
        }
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        foreach ($this->semuaUrlAdmin() as $alamat) {
            $this->get($alamat)->assertRedirect(route('login'));
        }
    }

    public function test_user_biasa_tidak_bisa_mengirim_aksi_admin(): void
    {
        $user = User::factory()->create();
        $koleksi = Collection::factory()->create();
        $reservasi = Reservation::factory()->create();

        $this->actingAs($user)->delete(route('admin.koleksi.destroy', $koleksi))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.reservasi.setujui', $reservasi))->assertForbidden();
        $this->actingAs($user)->post(route('admin.kategori.store'), ['name' => 'Kategori liar'])->assertForbidden();

        $this->assertModelExists($koleksi);
        $this->assertSame(Reservation::STATUS_PENDING, $reservasi->refresh()->status);
        $this->assertDatabaseMissing('categories', ['name' => 'Kategori liar']);
    }

    public function test_halaman_403_memberi_arahan_kembali(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))
            ->assertForbidden()->assertSee('403');
    }
}

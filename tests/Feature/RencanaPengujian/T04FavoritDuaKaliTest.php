<?php

namespace Tests\Feature\RencanaPengujian;

use App\Models\Collection;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * T04 · Simpan favorit dua kali
 * Hasil yang diharapkan: tidak ada duplikasi.
 */
class T04FavoritDuaKaliTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiga_kali_simpan_tetap_satu_baris(): void
    {
        $user = User::factory()->create();
        $koleksi = Collection::factory()->create();

        foreach (range(1, 3) as $_) {
            $this->actingAs($user)->post(route('favorit.store'), ['collection_id' => $koleksi->id])->assertRedirect();
        }

        $this->assertSame(1, Favorite::where('user_id', $user->id)->where('collection_id', $koleksi->id)->count());
        $this->actingAs($user)->get(route('favorit.index'))->assertOk()->assertSee($koleksi->name);
    }

    public function test_database_menolak_baris_kembar(): void
    {
        $favorit = Favorite::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        Favorite::query()->insert([
            'user_id' => $favorit->user_id,
            'collection_id' => $favorit->collection_id,
            'created_at' => now(),
        ]);
    }

    public function test_pengguna_berbeda_boleh_menyimpan_koleksi_yang_sama(): void
    {
        $koleksi = Collection::factory()->create();

        foreach (User::factory()->count(2)->create() as $user) {
            $this->actingAs($user)->post(route('favorit.store'), ['collection_id' => $koleksi->id]);
        }

        $this->assertSame(2, Favorite::where('collection_id', $koleksi->id)->count());
    }

    public function test_koleksi_yang_tidak_ada_ditolak(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('favorit.store'), ['collection_id' => 9999])
            ->assertSessionHasErrors('collection_id');

        $this->assertSame(0, Favorite::count());
    }
}

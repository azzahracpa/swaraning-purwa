<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_simpan_favorit_dari_detail_koleksi(): void
    {
        $koleksi = Collection::factory()->create();

        $this->actingAs($this->user)->from(route('koleksi.show', $koleksi))
            ->post(route('favorit.store'), ['collection_id' => $koleksi->id])
            ->assertRedirect(route('koleksi.show', $koleksi))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('favorites', ['user_id' => $this->user->id, 'collection_id' => $koleksi->id]);
    }

    public function test_simpan_favorit_dua_kali_tidak_menduplikasi(): void
    {
        $koleksi = Collection::factory()->create();

        $this->actingAs($this->user)->post(route('favorit.store'), ['collection_id' => $koleksi->id]);
        $this->actingAs($this->user)->post(route('favorit.store'), ['collection_id' => $koleksi->id])
            ->assertSessionHas('info');

        $this->assertSame(1, Favorite::where('user_id', $this->user->id)->count());
    }

    public function test_detail_koleksi_menunjukkan_sudah_tersimpan(): void
    {
        $koleksi = Collection::factory()->create();
        Favorite::factory()->for($this->user)->for($koleksi)->create();

        $this->actingAs($this->user)->get(route('koleksi.show', $koleksi))
            ->assertOk()->assertSee('Tersimpan di favorit');
    }

    public function test_halaman_favorit_hanya_milik_sendiri(): void
    {
        $milikSaya = Collection::factory()->create(['name' => 'Arca Milik Saya']);
        $milikLain = Collection::factory()->create(['name' => 'Arca Milik Orang Lain']);
        Favorite::factory()->for($this->user)->for($milikSaya)->create();
        Favorite::factory()->for(User::factory())->for($milikLain)->create();

        $this->actingAs($this->user)->get(route('favorit.index'))
            ->assertOk()->assertSee('Arca Milik Saya')->assertDontSee('Arca Milik Orang Lain');
    }

    public function test_hapus_favorit_dan_urungkan(): void
    {
        $koleksi = Collection::factory()->create();
        Favorite::factory()->for($this->user)->for($koleksi)->create();

        $this->actingAs($this->user)->from(route('favorit.index'))
            ->delete(route('favorit.destroy', $koleksi))
            ->assertRedirect(route('favorit.index'))
            ->assertSessionHas('favorit_dihapus');
        $this->assertDatabaseCount('favorites', 0);

        $this->actingAs($this->user)->post(route('favorit.store'), ['collection_id' => $koleksi->id]);
        $this->assertDatabaseCount('favorites', 1);
    }

    public function test_hapus_tidak_menyentuh_favorit_pengguna_lain(): void
    {
        $koleksi = Collection::factory()->create();
        Favorite::factory()->for(User::factory())->for($koleksi)->create();

        $this->actingAs($this->user)->delete(route('favorit.destroy', $koleksi));

        $this->assertDatabaseCount('favorites', 1);
    }

    public function test_halaman_favorit_kosong(): void
    {
        $this->actingAs($this->user)->get(route('favorit.index'))
            ->assertOk()->assertSee('Belum ada koleksi favorit');
    }
}

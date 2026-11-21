<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKategoriTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_bisa_menambah_dan_mengubah_kategori(): void
    {
        $this->actingAs($this->admin)->post(route('admin.kategori.store'), ['name' => 'Arca'])
            ->assertRedirect(route('admin.kategori.index'));

        $kategori = Category::where('name', 'Arca')->firstOrFail();

        $this->actingAs($this->admin)->put(route('admin.kategori.update', $kategori), [
            'name' => 'Arca', 'description' => 'Patung dari batu.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Patung dari batu.', $kategori->refresh()->description);
    }

    public function test_nama_kategori_harus_unik(): void
    {
        Category::factory()->create(['name' => 'Arca']);

        $this->actingAs($this->admin)->post(route('admin.kategori.store'), ['name' => 'Arca'])
            ->assertSessionHasErrors(['name' => 'Nama "Arca" sudah dipakai. Gunakan nama lain.']);
    }

    public function test_kategori_yang_dipakai_koleksi_tidak_bisa_dihapus(): void
    {
        $kategori = Category::factory()->create();
        Collection::factory()->for($kategori)->create();

        $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori))
            ->assertSessionHas('error');

        $this->assertModelExists($kategori);
    }

    public function test_kategori_kosong_bisa_dihapus(): void
    {
        $kategori = Category::factory()->create();

        $this->actingAs($this->admin)->delete(route('admin.kategori.destroy', $kategori))
            ->assertSessionHas('success');

        $this->assertModelMissing($kategori);
    }
}

<?php

namespace Tests\Feature;

use App\Models\MuseumProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilMuseumTest extends TestCase
{
    use RefreshDatabase;

    public function test_profil_museum_menampilkan_sejarah_dan_kontak(): void
    {
        $profil = MuseumProfile::factory()->create([
            'history' => "Paragraf sejarah pertama.\n\nParagraf sejarah kedua.",
            'opening_hours' => 'Selasa–Minggu, 08.00–15.00',
        ]);

        $this->get(route('profil-museum'))
            ->assertOk()
            ->assertSee('Sejarah singkat')
            ->assertSee('Paragraf sejarah kedua.')
            ->assertSee($profil->opening_hours);
    }

    public function test_profil_museum_kosong_menampilkan_pesan_ramah(): void
    {
        $this->get(route('profil-museum'))
            ->assertOk()
            ->assertSee('Profil museum belum diisi');
    }

    public function test_halaman_tidak_ada_memakai_tampilan_404_situs(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('belum ditemukan');
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanPublikTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_bisa_dibuka_tamu(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Swaraning')
            ->assertSee('Masuk');
    }

    public function test_halaman_login_dan_daftar_bisa_dibuka(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk');
        $this->get('/register')->assertOk()->assertSee('Daftar');
    }
}

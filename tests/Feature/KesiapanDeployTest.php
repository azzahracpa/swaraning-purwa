<?php

namespace Tests\Feature;

use App\Support\Gambar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KesiapanDeployTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_menjawab_json_tanpa_cookie_sesi(): void
    {
        $respons = $this->get('/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok', 'database' => 'ok']);

        $this->assertEmpty($respons->headers->getCookies(), '/health tidak boleh membuat sesi baru.');
    }

    public function test_berkas_deploy_tersedia_dan_tidak_memasukkan_env(): void
    {
        foreach (['Dockerfile.vercel', 'Caddyfile', 'vercel.json', '.dockerignore', 'DEPLOY.md'] as $berkas) {
            $this->assertFileExists(base_path($berkas));
        }

        $abaikan = file(base_path('.dockerignore'), FILE_IGNORE_NEW_LINES);
        $this->assertContains('.env', $abaikan);
        $this->assertContains('.env.*', $abaikan);

        $vercel = json_decode(file_get_contents(base_path('vercel.json')), true);
        $this->assertSame('Dockerfile.vercel', $vercel['services']['app']['entrypoint']);
        $this->assertSame('container', $vercel['services']['app']['runtime']);

        // config:cache tidak boleh dijalankan saat build (APP_KEY baru ada saat runtime)
        $docker = file_get_contents(base_path('Dockerfile.vercel'));
        $this->assertStringNotContainsString('artisan config:cache', $docker);
        $this->assertStringNotContainsString('artisan optimize', $docker);
        $this->assertStringContainsString('{$PORT:80}', file_get_contents(base_path('Caddyfile')));
    }

    public function test_nilai_bawaan_aplikasi_sesuai_museum_tanpa_env(): void
    {
        // Di Vercel tidak ada .env, jadi nilai bawaan config harus sudah benar
        $config = file_get_contents(config_path('app.php'));

        $this->assertStringContainsString("env('APP_NAME', 'Swaraning Purwa')", $config);
        $this->assertStringContainsString("env('APP_TIMEZONE', 'Asia/Jakarta')", $config);
        $this->assertStringContainsString("env('APP_LOCALE', 'id')", $config);
        $this->assertSame('Asia/Jakarta', config('app.timezone'));
    }

    public function test_disk_s3_melempar_galat_saat_gagal(): void
    {
        $this->assertTrue(config('filesystems.disks.s3.throw'));
    }

    public function test_gambar_tersimpan_tanpa_acl_per_berkas(): void
    {
        Storage::fake();

        $path = Gambar::simpan(UploadedFile::fake()->image('arca.jpg', 600, 600), 'koleksi');

        $this->assertStringStartsWith('koleksi/', $path);
        Storage::assertExists($path);
    }
}

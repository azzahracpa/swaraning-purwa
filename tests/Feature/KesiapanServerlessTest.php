<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class KesiapanServerlessTest extends TestCase
{
    public function test_health_check_bawaan_laravel(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_env_example_memuat_pengaturan_serverless(): void
    {
        $isi = file_get_contents(base_path('.env.example'));

        foreach ([
            'SESSION_DRIVER=database',
            'CACHE_STORE=database',
            'QUEUE_CONNECTION=sync',
            'LOG_CHANNEL=stderr',
            'FILESYSTEM_DISK=',
            'AWS_ENDPOINT=',
            'AWS_URL=',
            'AWS_USE_PATH_STYLE_ENDPOINT=',
        ] as $baris) {
            $this->assertStringContainsString($baris, $isi);
        }
    }

    public function test_disk_upload_mengikuti_env(): void
    {
        $this->assertArrayHasKey('s3', config('filesystems.disks'));
        $this->assertArrayHasKey('public', config('filesystems.disks'));
    }

    public function test_url_dipaksa_https_jika_app_url_https(): void
    {
        config(['app.url' => 'https://swaraning-purwa.contoh.test']);
        (new AppServiceProvider($this->app))->boot();

        $this->assertStringStartsWith('https://', url('/'));

        URL::forceScheme('http');
    }
}

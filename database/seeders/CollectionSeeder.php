<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = Category::pluck('id', 'name');

        // Semua data koleksi berlabel contoh dan perlu diverifikasi museum
        $koleksi = [
            [
                'kategori' => 'Arca',
                'name' => 'Arca Ganesha',
                'description' => '(contoh) Arca Ganesha digambarkan duduk dengan perut buncit dan memegang mangkuk. Ganesha dipercaya sebagai dewa ilmu pengetahuan dan penghalau rintangan.',
                'historical_period' => 'Abad ke-13 (contoh)',
                'origin' => 'Kota Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Ruang pamer utama',
                'gambar' => [
                    ['koleksi-arca-ganesha.svg', 'Tampak depan', true],
                    ['koleksi-arca-ganesha-samping.svg', 'Tampak samping', false],
                ],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Dwarapala',
                'description' => '(contoh) Dwarapala adalah arca penjaga pintu yang biasanya diletakkan berpasangan di depan bangunan suci.',
                'historical_period' => 'Abad ke-13 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Halaman museum',
                'gambar' => [
                    ['koleksi-arca-dwarapala.svg', 'Tampak depan', true],
                ],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Nandi',
                'description' => '(contoh) Nandi adalah lembu tunggangan Dewa Siwa yang digambarkan dalam posisi duduk.',
                'historical_period' => 'Abad ke-12 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Ruang pamer utama',
                'gambar' => [
                    ['koleksi-arca-nandi.svg', 'Tampak samping', true],
                ],
            ],
            [
                'kategori' => 'Prasasti',
                'name' => 'Prasasti Batu',
                'description' => '(contoh) Prasasti batu dengan aksara Jawa Kuno yang memuat penetapan sebuah wilayah sebagai sima (tanah perdikan).',
                'historical_period' => 'Abad ke-10 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Ruang prasasti',
                'gambar' => [
                    ['koleksi-prasasti-batu.svg', 'Tampak depan', true],
                    ['koleksi-prasasti-batu-detail.svg', 'Detail aksara', false],
                ],
            ],
            [
                'kategori' => 'Yoni dan Lingga',
                'name' => 'Yoni',
                'description' => '(contoh) Yoni berbentuk persegi dengan cerat di salah satu sisinya, menjadi pasangan lingga dalam pemujaan Siwa.',
                'historical_period' => 'Abad ke-9 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Halaman museum',
                'gambar' => [
                    ['koleksi-yoni.svg', 'Tampak atas', true],
                ],
            ],
            [
                'kategori' => 'Yoni dan Lingga',
                'name' => 'Lingga',
                'description' => '(contoh) Lingga berbentuk tiang dengan tiga bagian yang melambangkan Brahma, Wisnu, dan Siwa.',
                'historical_period' => 'Abad ke-9 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Ruang pamer utama',
                'gambar' => [
                    ['koleksi-lingga.svg', 'Tampak depan', true],
                ],
            ],
            [
                'kategori' => 'Relief',
                'name' => 'Panil Relief',
                'description' => '(contoh) Panil relief berpahat adegan cerita yang dulu menghiasi dinding bangunan suci.',
                'historical_period' => 'Abad ke-14 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Batu andesit',
                'location' => 'Ruang pamer utama',
                'gambar' => [
                    ['koleksi-relief-panil.svg', 'Tampak depan', true],
                ],
            ],
            [
                'kategori' => 'Gerabah',
                'name' => 'Kendi Terakota',
                'description' => '(contoh) Kendi dari tanah liat bakar yang dipakai sebagai wadah air.',
                'historical_period' => 'Abad ke-14 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Terakota',
                'location' => 'Vitrin gerabah',
                'gambar' => [
                    ['koleksi-kendi-terakota.svg', 'Tampak samping', true],
                ],
            ],
            [
                'kategori' => 'Gerabah',
                'name' => 'Wadah Gerabah',
                'description' => '(contoh) Wadah gerabah berbentuk mangkuk besar dengan hiasan garis di bagian bibir.',
                'historical_period' => 'Abad ke-14 (contoh)',
                'origin' => 'Malang (contoh)',
                'material' => 'Terakota',
                'location' => 'Vitrin gerabah',
                'gambar' => [
                    ['koleksi-gerabah-wadah.svg', 'Tampak samping', true],
                ],
            ],
        ];

        foreach ($koleksi as $data) {
            // Koleksi yang sudah ada (misal sudah diubah pengelola) dilewati,
            // jadi seeder aman dijalankan ulang di produksi
            $collection = Collection::firstOrCreate(['name' => $data['name']], [
                'category_id' => $kategori[$data['kategori']],
                'description' => $data['description'],
                'historical_period' => $data['historical_period'],
                'origin' => $data['origin'],
                'material' => $data['material'],
                'location' => $data['location'],
            ]);

            if (! $collection->wasRecentlyCreated) {
                continue;
            }

            // Gambar contoh adalah aset statis di public/images/contoh/
            foreach ($data['gambar'] as [$file, $caption, $utama]) {
                $collection->images()->create([
                    'image_url' => 'images/contoh/'.$file,
                    'caption' => $caption.' (foto contoh)',
                    'is_primary' => $utama,
                ]);
            }
        }
    }
}

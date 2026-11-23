<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['name' => 'Arca', 'description' => '(contoh) Patung dewa, tokoh, atau hewan suci dari batu.'],
            ['name' => 'Prasasti', 'description' => '(contoh) Batu atau logam bertulisan aksara kuno.'],
            ['name' => 'Yoni dan Lingga', 'description' => '(contoh) Benda pemujaan dari masa Hindu.'],
            ['name' => 'Relief', 'description' => '(contoh) Panil batu berpahat cerita atau hiasan.'],
            ['name' => 'Gerabah', 'description' => '(contoh) Wadah dari tanah liat bakar (terakota).'],
        ];

        foreach ($kategori as $data) {
            // firstOrCreate: aman dijalankan ulang, kategori yang sudah ada tidak diubah
            Category::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Kategori mengikuti jenis koleksi yang dipamerkan di Museum Mpu Purwa
        $kategori = [
            ['name' => 'Arca', 'description' => 'Patung dewa, dewi, bodhisatwa, tokoh, dan hewan suci dari masa Hindu-Buddha.'],
            ['name' => 'Prasasti', 'description' => 'Batu bertulisan aksara dan bahasa Jawa Kuno yang mencatat keputusan raja.'],
            ['name' => 'Benda Pemujaan', 'description' => 'Benda yang dipakai dalam upacara keagamaan, seperti yoni.'],
            ['name' => 'Fragmen Bangunan', 'description' => 'Bagian bangunan suci yang terlepas, seperti kemuncak, pipi tangga, ornamen, dan bata.'],
            ['name' => 'Alat Prasejarah', 'description' => 'Peralatan batu dari masa sebelum manusia mengenal tulisan.'],
            ['name' => 'Diorama', 'description' => 'Adegan sejarah Kerajaan Singhasari dan Majapahit dalam bentuk miniatur.'],
        ];

        foreach ($kategori as $data) {
            // firstOrCreate: aman dijalankan ulang, kategori yang sudah ada tidak diubah
            Category::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}

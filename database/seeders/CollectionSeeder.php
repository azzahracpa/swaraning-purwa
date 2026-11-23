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

        // Koleksi Museum Mpu Purwa yang fotonya tersedia di Wikimedia Commons
        // (kategori "Koleksi Museum Mpu Purwa"). Deskripsi disusun ulang dari
        // keterangan foto tersebut. Kolom yang belum diketahui sengaja dikosongkan.
        // Format foto: [nama berkas di public/images/museum, keterangan, fotografer, lisensi]
        $koleksi = [
            // ===== Arca =====
            [
                'kategori' => 'Arca',
                'name' => 'Arca Ganesha',
                'description' => "Ganesha digambarkan duduk seperti bayi dengan perut buncit. Rambutnya disanggul tinggi membentuk mahkota yang disebut jatamakuta.\n\nGanesha adalah putra Dewa Siwa dan Dewi Parwati. Ia dipuja sebagai dewa ilmu pengetahuan dan penghalau rintangan, sehingga arcanya banyak ditemukan di Jawa Timur.",
                'material' => 'Batu',
                'foto' => ['arca-ganesha', 'Tampak depan', 'Rhani Lilianti Kata', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Ganesha Bunulrejo',
                'description' => "Arca Ganesha ini duduk seperti bayi di atas bantalan berbentuk bunga teratai merah (padmasana).\n\nArca ini ditemukan di Kampung Beji, Kelurahan Bunulrejo, Kota Malang, sehingga diberi nama sesuai tempat temuannya.",
                'origin' => 'Kampung Beji, Bunulrejo, Kota Malang',
                'material' => 'Batu',
                'foto' => ['arca-ganesha-bunulrejo', 'Tampak depan', 'Xenovilius', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Ganesha Berlapik Bulat',
                'description' => "Ganesha duduk seperti bayi di atas lapik (alas) berbentuk bulat. Kedua tangan depannya masing-masing memegang mangkuk.\n\nBenda yang dibawa kedua tangan belakangnya sudah tidak dapat dikenali karena aus.",
                'material' => 'Batu',
                'foto' => ['arca-ganesha-berlapik-bulat', 'Tampak depan', 'Xenovilius', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Ganesha Tikus',
                'description' => "Arca Ganesha dengan badan tambun yang duduk seperti bayi. Posenya terlihat lucu, tetapi raut wajahnya justru tampak garang.\n\nNama arca ini merujuk pada tikus, hewan yang dalam kepercayaan Hindu menjadi wahana atau kendaraan Ganesha.",
                'material' => 'Batu',
                'foto' => ['arca-ganesha-tikus', 'Tampak depan', 'Farizor', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Dwarapala',
                'description' => "Dwarapala adalah arca raksasa penjaga pintu yang biasanya diletakkan berpasangan di depan bangunan suci.\n\nArca ini dipamerkan bersama arca lain. Ciri pembedanya ada pada gada (senjata pemukul) yang dipegang di sisi kanan.",
                'material' => 'Batu',
                'foto' => ['arca-dwarapala', 'Tampak depan', 'Ahmadrizky0102', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Lembu Nandi',
                'description' => "Lembu Nandi digambarkan dalam posisi rebah dengan kaki terlipat (njerum).\n\nNandi adalah lembu tunggangan Dewa Siwa. Seperti arca lembu lain dari masa Hindu, arca ini menjadi lambang kehadiran Siwa.",
                'material' => 'Batu',
                'foto' => ['arca-lembu-nandi', 'Tampak samping', 'Rhani Lilianti Kata', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Nandiswara',
                'description' => "Nandiswara berdiri tegak lurus. Tangan kanannya memegang trisula, sedangkan tangan kirinya memegang kain yang dikenakan.\n\nDi candi Hindu di Jawa, Nandiswara biasa ditempatkan sebagai penjaga pintu masuk bilik Siwa, berpasangan dengan Mahakala.",
                'material' => 'Batu',
                'foto' => ['arca-nandiswara', 'Tampak depan', 'Komengskuy', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Mahakala',
                'description' => "Mahakala berdiri tegak lurus (samabhangga). Tangan kanannya membawa gada, sedangkan telapak tangan kirinya sudah patah.\n\nMahakala adalah salah satu perwujudan Dewa Siwa dan sering menjadi pasangan Nandiswara sebagai penjaga pintu bangunan suci.",
                'material' => 'Batu',
                'foto' => ['arca-mahakala', 'Tampak depan', 'Syaasantoso', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Durga Mahisasuramardini',
                'description' => "Durga berdiri tegak dengan delapan tangan. Tangan kanannya membawa trisula dan kebut lalat serta menarik ekor lembu yang diinjaknya. Tangan kirinya membawa kerang bersayap, pedang, dan kapak, serta menarik tangan raksasa kecil yang keluar dari kepala lembu.\n\nArca ini menggambarkan kisah Dewi Durga mengalahkan Mahisasura, raksasa yang menjelma menjadi lembu.",
                'material' => 'Batu',
                'foto' => ['arca-durga-mahisasuramardini', 'Tampak depan', 'Xenovilius', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Wisnu',
                'description' => "Wisnu digambarkan duduk bersila dengan kaki bertumpang (wirasana) dan bertangan empat.\n\nTangan kanan belakangnya sudah pecah, sedangkan tangan kiri belakang membawa kerang bersayap (sangkha), salah satu atribut khas Wisnu.",
                'material' => 'Batu',
                'foto' => ['arca-wisnu', 'Tampak depan', 'Amidaxaviera', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Siwa Mahaguru',
                'description' => "Arca berdiri tegak lurus (samabhangga) dengan janggut runcing, perut buncit, dan senjata trisula.\n\nCiri-ciri itu menandai Siwa dalam wujudnya sebagai Mahaguru, yaitu guru suci yang bijaksana.",
                'material' => 'Batu',
                'foto' => ['arca-siwa-mahaguru', 'Tampak depan', 'Amidaxaviera', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Trimurti (Trisirah)',
                'description' => "Arca ini memiliki tiga kepala dengan ukuran yang kurang proporsional.\n\nKedua tangan di depan berada dalam sikap semadi sambil membawa bunga teratai merah.",
                'material' => 'Batu',
                'foto' => ['arca-trimurti', 'Tampak depan', 'Rhani Lilianti Kata', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Bodhisatwa Awalokiteswara',
                'description' => "Awalokiteswara berdiri dengan tubuh melengkung tiga (tribhangga). Lingkaran kesucian di belakang kepalanya berhias motif lidah api.\n\nTangan kanannya patah di bagian lengan, sedangkan tangan kiri memegang tangkai bunga teratai. Dalam Buddha Mahayana, Awalokiteswara adalah bodhisatwa welas asih.",
                'material' => 'Batu',
                'foto' => ['arca-awalokiteswara', 'Tampak depan', 'Annidafattiya', 'CC0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Bodhisatwa Manjusri',
                'description' => "Manjusri dipuja sebagai bodhisatwa, yaitu calon buddha, yang memiliki kebijaksanaan tertinggi.\n\nArca ini menjadi salah satu bukti pengaruh agama Buddha di wilayah Malang pada masa klasik.",
                'material' => 'Batu',
                'foto' => ['arca-manjusri', 'Tampak depan', 'Syaasantoso', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Sri Laksmi',
                'description' => "Sri Laksmi berdiri tegak lurus (samabhangga). Kedua tangannya memegang payudara (stanadwayamudra) yang dulunya berfungsi sebagai pancuran air.\n\nLaksmi adalah dewi kesuburan dan kemakmuran yang selalu dikaitkan dengan air suci (amerta).",
                'material' => 'Batu',
                'foto' => ['arca-sri-laksmi', 'Tampak depan', 'Fishynila', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Dewi',
                'description' => "Dari bentuk tubuhnya, tokoh yang digambarkan jelas seorang perempuan.\n\nNamun, atribut yang dipakainya tidak menunjuk pada dewi tertentu, baik dalam agama Hindu maupun Buddha, sehingga identitasnya belum dapat dipastikan.",
                'material' => 'Batu',
                'foto' => ['arca-dewi', 'Tampak depan', 'Amidaxaviera', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Perwujudan Dewi',
                'description' => "Arca perwujudan menggambarkan seorang tokoh, biasanya bangsawan yang telah wafat, dalam wujud dewa atau dewi.\n\nArca seperti ini dibuat untuk memuliakan tokoh tersebut di bangunan suci tempat ia didharmakan.",
                'material' => 'Batu',
                'foto' => ['arca-perwujudan-dewi', 'Tampak depan', 'Annidafattiya', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Dewi Ibu',
                'description' => "Arca ini dinamakan dewi ibu karena bentuknya sederhana dan menggambarkan seorang perempuan yang sedang menggendong anak.",
                'material' => 'Batu',
                'foto' => ['arca-dewi-ibu', 'Tampak depan', 'Syaasantoso', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Tokoh Pertapa',
                'description' => "Arca ini menggambarkan Siwa sebagai seorang pertapa.\n\nYang menarik perhatian adalah senjata keris yang diselipkan di bagian belakang pinggangnya.",
                'material' => 'Batu',
                'foto' => ['arca-tokoh-pertapa', 'Tampak depan', 'Annidafattiya', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Sangkhara',
                'description' => "Tokoh duduk bersila dengan kaki bertumpang (wirasana). Di belakang lehernya terdapat hiasan bulan sabit.\n\nKedua tangannya diletakkan di atas lutut sambil membawa bunga teratai merah.",
                'material' => 'Batu',
                'foto' => ['arca-sangkhara', 'Tampak depan', 'Annidafattiya', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Tokoh Dewa Berdiri',
                'description' => "Tokoh dewa berdiri dengan sikap tubuh sedikit melengkung (dwibhangga).\n\nTangan kanannya membawa tangkai teratai merah, tetapi telapak tangan kanan itu sudah patah.",
                'material' => 'Batu',
                'foto' => ['arca-tokoh-dewa-berdiri', 'Tampak depan', 'Ahmadrizky0102', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Tokoh Dewa Bermahkota',
                'description' => "Tokoh digambarkan duduk dan memakai mahkota yang bentuknya mirip mahkota wayang masa kini.\n\nSaat ditemukan, arca ini sulit diidentifikasi sehingga belum diketahui dewa yang digambarkan.",
                'material' => 'Batu',
                'foto' => ['arca-tokoh-dewa-bermahkota', 'Tampak depan', 'Komengskuy', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Arca Singa Stamba',
                'description' => "Arca seekor binatang ajaib dari mitologi Hindu-Buddha yang berwujud singa.",
                'material' => 'Batu',
                'foto' => ['arca-singa-stamba', 'Tampak depan', 'Syaasantoso', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Arca',
                'name' => 'Fragmen Arca Garuda Wisnu',
                'description' => "Arca ini masih berupa sketsa pahatan dan belum selesai dikerjakan.\n\nKarena belum selesai, belum jelas dewa siapa yang hendak digambarkan.",
                'material' => 'Batu',
                'foto' => ['fragmen-garuda-wisnu', 'Tampak depan', 'Amidaxaviera', 'CC BY 4.0'],
            ],

            // ===== Prasasti =====
            [
                'kategori' => 'Prasasti',
                'name' => 'Prasasti Dinoyo II',
                'description' => "Batu prasasti ini ditemukan pada tahun 1985 di sekitar pertigaan Jalan M.T. Haryono dan Jalan Gajayana, Kota Malang.\n\nPrasasti ditulis dengan aksara dan bahasa Jawa Kuno.",
                'origin' => 'Pertigaan Jl. M.T. Haryono dan Jl. Gajayana, Kota Malang',
                'material' => 'Batu',
                'foto' => ['prasasti-dinoyo-2', 'Tampak depan', 'Annidafattiya', 'CC0'],
            ],
            [
                'kategori' => 'Prasasti',
                'name' => 'Prasasti Muncang',
                'description' => "Prasasti ini berisi perintah Sri Maharaja Rake Hino Pu Sindok Sri Isana Wikramadharmottunggadewa melalui Rakai Kanuluhan.\n\nIsinya menetapkan sebidang tanah di Desa Muncang, yang termasuk wilayah Hujung, sebagai tanah dengan hak khusus.",
                'historical_period' => 'Masa pemerintahan Mpu Sindok (abad ke-10)',
                'material' => 'Batu',
                'foto' => ['prasasti-muncang', 'Tampak depan', 'Xenovilius', 'CC BY 4.0'],
            ],

            // ===== Benda Pemujaan =====
            [
                'kategori' => 'Benda Pemujaan',
                'name' => 'Yoni',
                'description' => "Yoni ini ditemukan pada tahun 1988. Pada tahun 2003, yoni dihibahkan oleh Institut Teknologi Palapa kepada Museum Mpu Purwa.\n\nYoni adalah pasangan lingga dalam pemujaan Siwa. Ceratnya dipakai untuk mengalirkan air suci saat upacara.",
                'material' => 'Batu',
                'foto' => ['yoni', 'Tampak samping', 'Rhani Lilianti Kata', 'CC BY 4.0'],
            ],

            // ===== Fragmen Bangunan =====
            [
                'kategori' => 'Fragmen Bangunan',
                'name' => 'Fragmen Kemuncak Candi',
                'description' => "Fragmen ini dulunya menghiasi bagian atap candi.\n\nHiasan serupa dapat ditemukan di Candi Badut di Malang dan di candi-candi Jawa Tengah.",
                'material' => 'Batu',
                'foto' => ['fragmen-kemuncak-candi', 'Tampak depan', 'Komengskuy', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Fragmen Bangunan',
                'name' => 'Fragmen Pipi Tangga',
                'description' => "Fragmen ini berasal dari patahan bagian samping tangga candi (pipi tangga).\n\nBentuknya melingkar seperti gelung dengan hiasan tumpal yang indah di bagian puncaknya.",
                'material' => 'Batu',
                'foto' => ['fragmen-pipi-tangga', 'Tampak samping', 'Komengskuy', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Fragmen Bangunan',
                'name' => 'Ornamen Kuncup Teratai',
                'description' => "Ornamen ini menghiasi bangunan suci agama Hindu dan Buddha.\n\nKuncup teratai melambangkan penciptaan dewa-dewi di kahyangan.",
                'material' => 'Batu',
                'foto' => ['ornamen-kuncup-teratai', 'Tampak depan', 'Rafliyan18', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Fragmen Bangunan',
                'name' => 'Bata Merah',
                'description' => "Bata merah menjadi bahan fondasi berbagai bangunan, mulai dari rumah, candi, hingga petirtaan (pemandian suci).",
                'material' => 'Tanah liat bakar',
                'foto' => ['bata-merah', 'Tampak atas', 'Rafliyan18', 'CC BY 4.0'],
            ],

            // ===== Alat Prasejarah =====
            [
                'kategori' => 'Alat Prasejarah',
                'name' => 'Batu Pelor',
                'description' => "Batu pelor adalah peninggalan budaya megalitik.\n\nFungsi dan cara penggunaannya sampai sekarang masih diteliti.",
                'material' => 'Batu',
                'foto' => ['batu-pelor', 'Tampak atas', 'Ahmadrizky0102', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Alat Prasejarah',
                'name' => 'Batu Pipisan',
                'description' => "Batu pipisan adalah alat untuk menghaluskan ramuan herbal yang dipakai sebagai obat.",
                'material' => 'Batu',
                'foto' => ['batu-pipisan', 'Tampak atas', 'Ahmadrizky0102', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Alat Prasejarah',
                'name' => 'Kapak Genggam',
                'description' => "Kapak genggam adalah alat batu yang dipakai langsung dengan digenggam, tanpa tangkai.\n\nAlat seperti ini dibuat dengan memangkas batu hingga salah satu sisinya tajam.",
                'material' => 'Batu',
                'foto' => ['kapak-genggam', 'Tampak atas', 'Rafliyan18', 'CC0'],
            ],

            // ===== Diorama =====
            [
                'kategori' => 'Diorama',
                'name' => 'Diorama Keruntuhan Singhasari dan Berdirinya Majapahit',
                'description' => "Diorama di lantai dua museum ini menceritakan keruntuhan Kerajaan Singhasari dan pendirian Kerajaan Majapahit.",
                'location' => 'Lantai 2',
                'foto' => ['diorama-singhasari-majapahit', 'Tampak depan', 'Farizor', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Diorama',
                'name' => 'Diorama Ekspedisi Pamalayu',
                'description' => "Diorama ini menggambarkan perluasan pengaruh Kerajaan Singhasari ke kerajaan-kerajaan di tanah Melayu melalui Ekspedisi Pamalayu.",
                'foto' => ['diorama-pamalayu', 'Tampak depan', 'Rafliyan18', 'CC BY 4.0'],
            ],
            [
                'kategori' => 'Diorama',
                'name' => 'Diorama Pembangunan Candi Jago',
                'description' => "Diorama ini menggambarkan pembangunan Candi Jago, peninggalan Kerajaan Singhasari di Kecamatan Tumpang, Kabupaten Malang.",
                'foto' => ['diorama-candi-jago', 'Tampak depan', 'Rafliyan18', 'CC BY 4.0'],
            ],
        ];

        foreach ($koleksi as $data) {
            // Koleksi yang sudah ada (misal sudah diubah pengelola) dilewati,
            // jadi seeder aman dijalankan ulang di produksi
            $collection = Collection::firstOrCreate(['name' => $data['name']], [
                'category_id' => $kategori[$data['kategori']],
                'description' => $data['description'],
                'historical_period' => $data['historical_period'] ?? null,
                'origin' => $data['origin'] ?? null,
                'material' => $data['material'] ?? null,
                'location' => $data['location'] ?? null,
            ]);

            if (! $collection->wasRecentlyCreated) {
                continue;
            }

            // Foto adalah aset statis di public/images/museum/, nama fotografer
            // dan lisensinya dicantumkan di keterangan foto
            [$file, $keterangan, $fotografer, $lisensi] = $data['foto'];
            $collection->images()->create([
                'image_url' => 'images/museum/'.$file.'.jpg',
                'caption' => "{$keterangan}. Foto: {$fotografer} ({$lisensi}), Wikimedia Commons",
                'is_primary' => true,
            ]);
        }
    }
}

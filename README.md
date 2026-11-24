# Swaraning Purwa

Website informasi dan edukasi digital **Museum Mpu Purwa Kota Malang**.
Project akhir mata kuliah Pengembangan Aplikasi Web Lanjut, FILKOM UB.

> Situs ini adalah prototipe akademik, **bukan situs resmi Museum Mpu Purwa**.
> Foto dan deskripsi koleksi bersumber dari Wikimedia Commons (lihat [Sumber foto](#sumber-foto)),
> sedangkan alamat, telepon, dan jam buka diambil dari liputan media. Data ini belum diverifikasi
> langsung oleh pihak museum.

## Alamat aplikasi

| Lingkungan | Alamat |
| --- | --- |
| Produksi (Vercel) | https://swaraning-purwa.vercel.app |
| Laptop | http://localhost:8000 |

Pemeriksaan cepat: buka `/health`, hasilnya `{"status":"ok","database":"ok"}`.

## Fitur

| Peran | Yang bisa dilakukan |
| --- | --- |
| Pengunjung (tanpa akun) | Beranda, profil museum, katalog koleksi dengan pencarian, filter kategori, dan urutan, detail koleksi dengan galeri foto, artikel |
| Pengguna (akun user) | Semua fitur pengunjung, simpan koleksi favorit, ajukan reservasi kunjungan dan lihat riwayat/kode booking, batalkan pengajuan, kerjakan kuis dan lihat skor, dashboard ringkasan, ubah profil |
| Pengelola (akun admin) | Dashboard statistik dan grafik 7 hari, kelola koleksi beserta foto, kategori, artikel (draf/terbit/terjadwal), profil museum, jadwal kunjungan, persetujuan reservasi dengan cek kapasitas, kuis beserta soal dan opsi |

## Anggota dan modul

| Anggota | Modul utama |
| --- | --- |
| Amel | Beranda, profil museum, artikel (publik dan admin) |
| Devi | Kategori, katalog, detail koleksi, foto koleksi, favorit |
| Zahra | Database, migrasi, autentikasi, role, dashboard user, deploy |
| Nofita | Jadwal kunjungan, reservasi, riwayat, persetujuan |
| Izza | Kuis, penilaian, dashboard admin |

## Teknologi

- Laravel 13 (PHP 8.3 ke atas), MySQL
- Blade, Tailwind CSS 3 lewat Vite, Alpine.js
- Laravel Breeze (stack Blade) untuk autentikasi
- Font Cormorant Garamond dan Inter dibundel lewat `@fontsource` (tanpa CDN)
- PHPUnit untuk pengujian otomatis

Produksi:

| Bagian | Layanan |
| --- | --- |
| Aplikasi | Vercel, runtime container FrankenPHP, region Singapura (`sin1`) |
| Basis data | TiDB Cloud Starter (kompatibel MySQL), region Singapura |
| Foto unggahan admin | Supabase Storage lewat protokol S3, region Singapura |

Aplikasi dan database sengaja berada di region yang sama. Saat aplikasi masih berjalan di
Washington (`iad1`), setiap halaman butuh 5 sampai 7 detik dan halaman Jadwal Kunjungan sempat
error 500. Setelah dipindah ke Singapura, halaman terbuka dalam kurang dari 1 detik.

## Menjalankan di laptop

Kebutuhan: PHP 8.3+, Composer, Node.js 20+, MySQL 8+, Git.

```bash
git clone <url-repo> swaraning-purwa
cd swaraning-purwa
cp .env.example .env          # Windows PowerShell: Copy-Item .env.example .env
composer install
npm install
php artisan key:generate
```

Buat database kosong `swaraning_purwa` di MySQL, isi `DB_USERNAME` dan `DB_PASSWORD` di `.env`, lalu:

```bash
php artisan storage:link
php artisan migrate:fresh --seed
npm run dev            # terminal 1
php artisan serve      # terminal 2
```

Buka http://localhost:8000

### Akun contoh dari seeder (laptop)

| Peran | Email | Kata sandi |
| --- | --- | --- |
| Admin | admin@swaraning-purwa.test | `password` |
| User | pengguna@swaraning-purwa.test | `password` |
| User | pengguna2@swaraning-purwa.test | `password` |

Kata sandi bisa diganti lewat variabel `SEED_ADMIN_PASSWORD` dan `SEED_USER_PASSWORD`.
Di produksi seeder **menolak berjalan** bila kata sandi masih `password` atau kurang dari 10 karakter.
Kata sandi akun demo produksi dibagikan langsung kepada dosen, tidak ditulis di repo.

Seeder aman dijalankan ulang (`php artisan db:seed`): data yang sudah ada, termasuk yang sudah
diubah pengelola, tidak ditimpa dan tidak dobel.

| Data awal | Isi |
| --- | --- |
| Akun | 1 admin dan 2 pengguna |
| Kategori | 6: Arca, Prasasti, Benda Pemujaan, Fragmen Bangunan, Alat Prasejarah, Diorama |
| Koleksi | 37 koleksi Museum Mpu Purwa, masing-masing dengan foto asli dari Wikimedia Commons |
| Artikel | 8 (7 terbit, 1 draf) |
| Profil museum | Sejarah singkat, alamat, telepon, dan jam buka |
| Jadwal | 4 pekan ke depan, 3 slot per hari (09.00, 10.30, 13.00), Senin libur |
| Kuis | 3 (2 aktif, 1 draf) |
| Reservasi | 6 contoh dengan berbagai status |

Kolom periode, asal, dan lokasi koleksi hanya diisi kalau sumbernya menyebutkan, supaya tidak ada
informasi karangan. Admin dapat melengkapinya lewat halaman Kelola Koleksi.

## Struktur dan konvensi kode

Kode mengikuti pola yang diajarkan di materi PAWL01 sampai PAWL05.

```
app/Http/Controllers/
├── Auth/        controller bawaan Breeze (login, daftar, logout, ubah password)
├── Publik/      halaman tanpa login (beranda, profil, katalog, artikel)
├── User/        halaman user yang login (dashboard, profil, reservasi, favorit, kuis)
└── Admin/       halaman pengelola (CRUD konten, jadwal, reservasi, kuis)
app/Http/Middleware/EnsureUserHasRole.php   middleware role:admin / role:user
app/Http/Requests/                           Form Request (StoreXRequest, UpdateXRequest)
app/Models/                                  14 model sesuai ERD
app/Support/Navigasi.php                     helper menu
config/menu.php                              daftar menu navbar dan sidebar
routes/web.php                               pemuat otomatis routes/modules/*.php
routes/modules/                              satu file route per modul
resources/views/components/                  komponen Blade design system
resources/views/layouts/                     publik, user, admin, guest
resources/views/partials/                    navbar, footer, sidebar
```

Aturan tim:

1. **Route per modul.** Setiap modul punya file sendiri di `routes/modules/`, misalnya
   `katalog.php`, `artikel.php`, `reservasi.php`. File dimuat otomatis, jadi tidak perlu
   mengubah `routes/web.php` dan tidak terjadi konflik saat merge.
2. **Route bernama dan resource controller.** Pakai `Route::resource()` dan route model binding.
   Route admin memakai `prefix('admin')`, `name('admin.')`, dan `middleware(['auth', 'role:admin'])`.
3. **Validasi lewat Form Request** (`StoreXRequest`, `UpdateXRequest`), simpan dengan
   `$request->validated()`. Kolom yang boleh diisi massal didaftarkan di `$fillable`.
4. **Pola POST → Redirect → GET** dengan flash message:
   `return redirect()->route('admin.koleksi.index')->with('success', 'Koleksi berhasil ditambahkan.');`
   Pesan tampil otomatis di layout lewat komponen `<x-flash />`.
5. **Form** selalu memakai `@csrf`, `@method('PUT')` atau `@method('DELETE')`, `@error`, dan `old()`.
   Komponen `<x-kolom-isian>` sudah mengurus label, `old()`, dan pesan galat.
6. **Konfirmasi hapus** memakai dialog bersama:
   ```blade
   <form method="POST" action="{{ route('admin.koleksi.destroy', $koleksi) }}" x-data
         x-on:submit.prevent="$store.konfirmasi.buka($el, {
             judul: 'Hapus koleksi ini?',
             isi: 'Foto koleksi ikut terhapus dan tidak bisa dikembalikan.',
             ringkasan: @js($koleksi->name),
             tombol: 'Hapus permanen',
         })">
       @csrf
       @method('DELETE')
       <button type="submit" class="tombol tombol-bahaya tombol-kecil">Hapus</button>
   </form>
   ```
   Untuk setujui reservasi pakai `jenis: 'setujui'`.
7. **Eager loading** untuk relasi yang ditampilkan di daftar, misal `Collection::with(['category', 'primaryImage'])`.
8. Teks antarmuka berbahasa Indonesia.
9. **Hindari query di dalam perulangan.** Hitung jumlah atau total sekali lewat `withSum`,
   `withCount`, atau eager loading, lalu pakai hasilnya di view. Database produksi berada di
   server lain, sehingga setiap query tambahan menambah waktu muat halaman.

### Nama route yang disepakati

Menu di `config/menu.php` sudah menunjuk nama route berikut. Selama route belum dibuat,
link otomatis mengarah ke `#`.

| Modul | Nama route |
| --- | --- |
| Beranda, profil (Amel) | `beranda`, `profil-museum`, `artikel.index`, `artikel.show`, `admin.artikel.*`, `admin.profil-museum.edit`, `admin.profil-museum.update` |
| Katalog, favorit (Devi) | `koleksi.index` (parameter pencarian `?cari=`, filter `?kategori=`), `koleksi.show`, `favorit.index`, `favorit.store`, `favorit.destroy`, `admin.kategori.*`, `admin.koleksi.*` |
| Akun (Zahra) | `login`, `register`, `logout`, `dashboard`, `profile.edit`, `profile.update` |
| Reservasi (Nofita) | `reservasi.index`, `reservasi.store`, `reservasi.riwayat`, `reservasi.show`, `reservasi.batal`, `admin.jadwal.*`, `admin.reservasi.*` |
| Kuis, dashboard admin (Izza) | `kuis.index`, `kuis.mulai`, `kuis.kerjakan`, `kuis.kirim`, `kuis.hasil`, `admin.kuis.*`, `admin.dashboard` |
| Deploy (Zahra) | `health` (`GET /health`) |

## Basis data

Mengikuti ERD di dokumen perancangan: **14 tabel**

`users`, `museum_profiles`, `categories`, `collections`, `collection_images`, `favorites`,
`articles`, `visit_schedules`, `reservations`, `quizzes`, `questions`, `answer_options`,
`quiz_attempts`, `quiz_answers`.

Aturan integritas yang sudah dipasang di migration:

- `users.email` dan `reservations.booking_code` unik.
- `favorites` unik pada `(user_id, collection_id)`.
- `quiz_answers` unik pada `(attempt_id, question_id)`.
- Kategori yang masih punya koleksi, jadwal yang sudah dipakai reservasi, dan admin yang masih
  punya artikel tidak bisa dihapus (restrict). Foto koleksi, soal, dan opsi ikut terhapus (cascade).

Kolom timestamp mengikuti kamus data: misalnya `categories` tanpa timestamp, `favorites` dan
`quizzes` hanya `created_at`, `museum_profiles` hanya `updated_at`.

### Tambahan teknis di luar ERD

Hanya tabel bawaan Laravel berikut, **tanpa mengubah 14 tabel utama**:

| Tabel | Alasan |
| --- | --- |
| `sessions` | `SESSION_DRIVER=database`, karena server Vercel tidak punya disk permanen |
| `cache`, `cache_locks` | `CACHE_STORE=database`, alasan yang sama |
| `migrations` | catatan migrasi otomatis milik Laravel |

Fitur Breeze yang membutuhkan kolom atau tabel di luar ERD tidak dipakai, dan memang tidak ada
di desain: "Ingat saya" (`remember_token`), verifikasi email (`email_verified_at`), dan lupa
password (`password_reset_tokens`). Tabel antrean (`jobs`) juga tidak dibuat karena
`QUEUE_CONNECTION=sync`.

## Kesiapan serverless (Vercel)

| Hal | Pengaturan |
| --- | --- |
| Sesi, cache, antrean | `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=sync` |
| Log | `LOG_CHANNEL=stderr` (dibaca di menu Logs Vercel) |
| Upload gambar | `Storage` facade dengan disk dari `FILESYSTEM_DISK`. Lokal `public`, produksi `s3` (S3 kompatibel) |
| Foto koleksi seeder | aset statis di `public/images/museum/`, ikut di repo (sumber dan lisensi di bagian Sumber foto) |
| Proxy dan HTTPS | `trustProxies(at: '*')` di `bootstrap/app.php`; kalau `APP_URL` diawali `https://`, semua URL dipaksa https |
| Aset Vite | dibangun saat build (`npm run build`), folder `public/build` tidak di-commit |
| Rahasia | tidak ada `.env` di repo; semua variabel ada di `.env.example` beserta penjelasannya |
| Nilai bawaan | tanpa `.env` pun nama aplikasi "Swaraning Purwa", bahasa Indonesia, zona waktu `Asia/Jakarta` |
| Pemeriksaan | `GET /health` mengecek aplikasi dan koneksi database, tanpa sesi dan cookie |
| Container | `Dockerfile.vercel` (FrankenPHP), `Caddyfile`, `vercel.json`, `.dockerignore` |

Langkah deploy lengkap, termasuk pilihan MySQL dan penyimpanan S3 kompatibel, variabel
Vercel untuk Preview dan Production, migrasi dari laptop, dan pemecahan masalah, ada di
[DEPLOY.md](DEPLOY.md).

Contoh menyimpan dan menampilkan upload:

```php
// simpan (controller)
$path = $request->file('foto')->store('koleksi');          // disk mengikuti FILESYSTEM_DISK
$koleksi->images()->create(['image_url' => $path, 'is_primary' => true]);

// tampilkan (model / view)
$image->url   // accessor memakai Storage::url(), foto bawaan seeder memakai asset()
```

Jangan menulis path `public/storage/...` secara langsung.

## Design system

Tema Tailwind (`tailwind.config.js`) diambil dari variables Figma: warna putih, ivory `#F7F3EB`,
maroon `#702F36`, dark brown `#3B2927`, gold `#C6A15B`; tipografi Cormorant Garamond (judul) dan
Inter (isi); radius kecil 4, input 6, kartu 14, relung untuk foto koleksi.

Kelas siap pakai di `resources/css/app.css`: `wadah`, `teks-h1`, `teks-h2`, `teks-h3`,
`teks-display-l`, `aksen-miring`, `tombol tombol-utama | tombol-garis | tombol-terang | tombol-bahaya`,
`tombol-kecil`, `tombol-ikon`, `tautan`, `kolom-input`, `kartu`, `tabel`.

Komponen Blade:

| Komponen | Contoh |
| --- | --- |
| Ikon | `<x-ikon nama="cari" class="size-5" />` |
| Kolom isian | `<x-kolom-isian name="title" label="Judul" :value="$artikel->title" wajib />` |
| Badge status reservasi | `<x-badge-status :status="$reservasi->status" />` |
| Badge keadaan (admin) | `<x-badge-keadaan nada="positif">Aktif</x-badge-keadaan>` |
| Label seksi | `<x-label-seksi nomor="02" label="Koleksi unggulan" />` |
| Tempat foto | `<x-tempat-foto class="aspect-[4/5] rounded-relung" keterangan="Arca" />` |
| Peringatan | `<x-peringatan jenis="galat" judul="Gagal">Pesan</x-peringatan>` |
| Keadaan kosong | `<x-keadaan-kosong judul="Belum ada reservasi" />` |
| Breadcrumb | `<x-breadcrumb :items="['Beranda' => route('beranda'), 'Katalog' => null]" />` |
| Paginasi | otomatis dari `{{ $data->links() }}` |

Interaksi ringan (Alpine.js, menghormati pengaturan "kurangi gerakan"):

| Directive | Fungsi |
| --- | --- |
| `x-geser` | daftar horizontal bisa digeser dengan klik dan tarik (galeri, slot jadwal, chip) |
| `x-muncul="150"` | elemen muncul pelan saat discroll, angka = jeda milidetik |
| `x-miring="5"` | kartu sedikit miring mengikuti mouse |
| `$store.konfirmasi` | dialog konfirmasi hapus, setujui, tolak, batal |

Layout: `@extends('layouts.publik')`, `@extends('layouts.user')` (punya `@section('kepala')`
untuk judul besar), `@extends('layouts.admin')` (punya `@section('halaman')` untuk breadcrumb).

## Pengujian

```bash
php artisan test
```

Tes memakai SQLite di memori (lihat `phpunit.xml`), jadi tidak menyentuh database MySQL lokal.
Saat ini terdapat **207 tes** dan semuanya lulus.
Menjalankan satu berkas atau satu skenario saja:

```bash
php artisan test tests/Feature/RencanaPengujian
php artisan test --filter=T06
```

### Rencana pengujian (dokumen perancangan bab 6.5)

Setiap skenario punya satu berkas di `tests/Feature/RencanaPengujian/`. Selain kasus utama,
tiap berkas juga menguji kasus tepi yang berkaitan.

| ID | Skenario | Hasil yang diharapkan | Berkas tes | Penanggung jawab |
| --- | --- | --- | --- | --- |
| T01 | Registrasi dengan email sama | Ditolak dengan pesan validasi | `T01RegistrasiEmailSamaTest` | Zahra |
| T02 | User membuka URL admin | Akses ditolak (403, tamu ke login) | `T02UserMembukaUrlAdminTest` | Zahra |
| T03 | Cari kategori Arca | Hanya koleksi terkait tampil | `T03CariKategoriArcaTest` | Devi |
| T04 | Simpan favorit dua kali | Tidak ada duplikasi | `T04FavoritDuaKaliTest` | Devi |
| T05 | Reservasi tanpa jumlah pengunjung | Form ditolak | `T05ReservasiTanpaJumlahTest` | Nofita |
| T06 | Approval melebihi kapasitas | Sistem menolak persetujuan | `T06ApprovalMelebihiKapasitasTest` | Nofita |
| T07 | User melihat reservasi user lain | Tidak dapat diakses (403) | `T07ReservasiUserLainTest` | Zahra |
| T08 | Submit kuis | Skor sesuai kunci jawaban | `T08SubmitKuisTest` | Izza |
| T09 | Pilih opsi dari soal berbeda | Jawaban ditolak | `T09OpsiSoalBerbedaTest` | Izza |
| T10 | Unggah file non-gambar | Ditolak di semua form gambar | `T10UnggahNonGambarTest` | Amel |

Tes lain yang penting:

| Berkas tes | Yang diuji |
| --- | --- |
| `StrukturDatabaseTest` | 14 tabel dan kolom sesuai ERD, seeder dan relasi berjalan |
| `Seeder*Test` | Semua seeder aman dijalankan ulang dan tidak menimpa suntingan pengelola |
| `KesiapanDeployTest`, `KesiapanServerlessTest` | Route `/health`, berkas deploy, disk S3, HTTPS |
| `PerbaikanProduksiTest` | Halaman Jadwal tidak menjalankan query per slot, admin yang membuka halaman pengguna dialihkan ke `/admin` |

Catatan T10: `UploadedFile::fake()` melaporkan tipe MIME dari **nama** berkas, sehingga berkas teks
bernama `.jpg` bisa lolos di tes padahal di aplikasi sungguhan ditolak (Laravel membaca isi berkas).
Karena itu tes T10 membuat berkas teks sungguhan untuk kasus tersebut.

## Sumber foto

Semua foto di `public/images/museum/` berasal dari Wikimedia Commons (kategori Museum Mpu Purwa) dan dipakai sesuai lisensinya. Foto sudah diperkecil. Nama fotografer juga tampil di keterangan foto pada halaman detail koleksi.

| Berkas | Fotografer | Lisensi | Sumber |
|---|---|---|---|
| `arca-ganesha.jpg` | Rhani Lilianti Kata | CC BY 4.0 | [File:Arca Ganesya.jpg](https://commons.wikimedia.org/wiki/File%3AArca_Ganesya.jpg) |
| `arca-ganesha-bunulrejo.jpg` | Xenovilius | CC BY 4.0 | [File:Ganesya Bunulrejo.jpg](https://commons.wikimedia.org/wiki/File%3AGanesya_Bunulrejo.jpg) |
| `arca-ganesha-berlapik-bulat.jpg` | Xenovilius | CC BY 4.0 | [File:Ganesya.jpg](https://commons.wikimedia.org/wiki/File%3AGanesya.jpg) |
| `arca-ganesha-tikus.jpg` | Farizor | CC BY 4.0 | [File:Arca Ganesya Tikus.jpg](https://commons.wikimedia.org/wiki/File%3AArca_Ganesya_Tikus.jpg) |
| `arca-dwarapala.jpg` | Ahmadrizky0102 | CC BY 4.0 | [File:Dwarapala.jpg](https://commons.wikimedia.org/wiki/File%3ADwarapala.jpg) |
| `arca-lembu-nandi.jpg` | Rhani Lilianti Kata | CC BY 4.0 | [File:Lembu Nandi.jpg](https://commons.wikimedia.org/wiki/File%3ALembu_Nandi.jpg) |
| `arca-nandiswara.jpg` | Komengskuy | CC BY 4.0 | [File:Koleksi Nandiswara.jpg](https://commons.wikimedia.org/wiki/File%3AKoleksi_Nandiswara.jpg) |
| `arca-mahakala.jpg` | Syaasantoso | CC BY 4.0 | [File:Mahakala Museum Mpu Purwa.jpg](https://commons.wikimedia.org/wiki/File%3AMahakala_Museum_Mpu_Purwa.jpg) |
| `arca-durga-mahisasuramardini.jpg` | Xenovilius | CC BY 4.0 | [File:Durgamahisa Suramardini.jpg](https://commons.wikimedia.org/wiki/File%3ADurgamahisa_Suramardini.jpg) |
| `arca-wisnu.jpg` | Amidaxaviera | CC BY 4.0 | [File:Wisnu.jpg](https://commons.wikimedia.org/wiki/File%3AWisnu.jpg) |
| `arca-siwa-mahaguru.jpg` | Amidaxaviera | CC BY 4.0 | [File:Siwa Mahaguru.jpg](https://commons.wikimedia.org/wiki/File%3ASiwa_Mahaguru.jpg) |
| `arca-trimurti.jpg` | Rhani Lilianti Kata | CC BY 4.0 | [File:Trimurti atau Trisirah.jpg](https://commons.wikimedia.org/wiki/File%3ATrimurti_atau_Trisirah.jpg) |
| `arca-awalokiteswara.jpg` | Annidafattiya | CC0 | [File:Bodhisatwa (Awalokiteswara).jpg](https://commons.wikimedia.org/wiki/File%3ABodhisatwa_%28Awalokiteswara%29.jpg) |
| `arca-manjusri.jpg` | Syaasantoso | CC BY 4.0 | [File:Bodhisatwa Manjuseri.jpg](https://commons.wikimedia.org/wiki/File%3ABodhisatwa_Manjuseri.jpg) |
| `arca-sri-laksmi.jpg` | Fishynila | CC BY 4.0 | [File:Dewi Kesuburan (Sri Laksmi).jpg](https://commons.wikimedia.org/wiki/File%3ADewi_Kesuburan_%28Sri_Laksmi%29.jpg) |
| `arca-dewi.jpg` | Amidaxaviera | CC BY 4.0 | [File:Arca Dewi.jpg](https://commons.wikimedia.org/wiki/File%3AArca_Dewi.jpg) |
| `arca-perwujudan-dewi.jpg` | Annidafattiya | CC BY 4.0 | [File:01 Arca Perwujudan Dewi Mpu Purwa Museum.jpg](https://commons.wikimedia.org/wiki/File%3A01_Arca_Perwujudan_Dewi_Mpu_Purwa_Museum.jpg) |
| `arca-dewi-ibu.jpg` | Syaasantoso | CC BY 4.0 | [File:Tokoh Dewi Ibu.jpg](https://commons.wikimedia.org/wiki/File%3ATokoh_Dewi_Ibu.jpg) |
| `arca-tokoh-pertapa.jpg` | Annidafattiya | CC BY 4.0 | [File:Arca Tokoh Pertapa.jpg](https://commons.wikimedia.org/wiki/File%3AArca_Tokoh_Pertapa.jpg) |
| `arca-sangkhara.jpg` | Annidafattiya | CC BY 4.0 | [File:Arca Sangkhara.jpg](https://commons.wikimedia.org/wiki/File%3AArca_Sangkhara.jpg) |
| `arca-tokoh-dewa-berdiri.jpg` | Ahmadrizky0102 | CC BY 4.0 | [File:Tokoh Dewa.jpg](https://commons.wikimedia.org/wiki/File%3ATokoh_Dewa.jpg) |
| `arca-tokoh-dewa-bermahkota.jpg` | Komengskuy | CC BY 4.0 | [File:Koleksi Tokoh Dewa.jpg](https://commons.wikimedia.org/wiki/File%3AKoleksi_Tokoh_Dewa.jpg) |
| `arca-singa-stamba.jpg` | Syaasantoso | CC BY 4.0 | [File:Singa Stamba.jpg](https://commons.wikimedia.org/wiki/File%3ASinga_Stamba.jpg) |
| `fragmen-garuda-wisnu.jpg` | Amidaxaviera | CC BY 4.0 | [File:Fragmen Garuda Wisnu.jpg](https://commons.wikimedia.org/wiki/File%3AFragmen_Garuda_Wisnu.jpg) |
| `prasasti-dinoyo-2.jpg` | Annidafattiya | CC0 | [File:Prasasti Dinoyo 2.jpg](https://commons.wikimedia.org/wiki/File%3APrasasti_Dinoyo_2.jpg) |
| `prasasti-muncang.jpg` | Xenovilius | CC BY 4.0 | [File:Prasasti Muncang.png](https://commons.wikimedia.org/wiki/File%3APrasasti_Muncang.png) |
| `yoni.jpg` | Rhani Lilianti Kata | CC BY 4.0 | [File:Yoni di Museum Mpu Purwa.jpg](https://commons.wikimedia.org/wiki/File%3AYoni_di_Museum_Mpu_Purwa.jpg) |
| `fragmen-kemuncak-candi.jpg` | Komengskuy | CC BY 4.0 | [File:Fragmen Kemuncak Candi.jpg](https://commons.wikimedia.org/wiki/File%3AFragmen_Kemuncak_Candi.jpg) |
| `fragmen-pipi-tangga.jpg` | Komengskuy | CC BY 4.0 | [File:Fragmen Pipi Tangga.jpg](https://commons.wikimedia.org/wiki/File%3AFragmen_Pipi_Tangga.jpg) |
| `ornamen-kuncup-teratai.jpg` | Rafliyan18 | CC BY 4.0 | [File:Kuncup teratai.jpg](https://commons.wikimedia.org/wiki/File%3AKuncup_teratai.jpg) |
| `bata-merah.jpg` | Rafliyan18 | CC BY 4.0 | [File:Bata Merah.jpg](https://commons.wikimedia.org/wiki/File%3ABata_Merah.jpg) |
| `batu-pelor.jpg` | Ahmadrizky0102 | CC BY 4.0 | [File:Batu Pelor.jpg](https://commons.wikimedia.org/wiki/File%3ABatu_Pelor.jpg) |
| `batu-pipisan.jpg` | Ahmadrizky0102 | CC BY 4.0 | [File:Batu Pipisan.jpg](https://commons.wikimedia.org/wiki/File%3ABatu_Pipisan.jpg) |
| `kapak-genggam.jpg` | Rafliyan18 | CC0 | [File:Kapak Genggam di Museum Mpu Purwa.jpg](https://commons.wikimedia.org/wiki/File%3AKapak_Genggam_di_Museum_Mpu_Purwa.jpg) |
| `diorama-singhasari-majapahit.jpg` | Farizor | CC BY 4.0 | [File:Diorama Lantai 2 Museum Mpu Purwa.jpg](https://commons.wikimedia.org/wiki/File%3ADiorama_Lantai_2_Museum_Mpu_Purwa.jpg) |
| `diorama-pamalayu.jpg` | Rafliyan18 | CC BY 4.0 | [File:Diorama Pamalayu.jpg](https://commons.wikimedia.org/wiki/File%3ADiorama_Pamalayu.jpg) |
| `diorama-candi-jago.jpg` | Rafliyan18 | CC BY 4.0 | [File:Diorama Pembangunan Candi Jago.jpg](https://commons.wikimedia.org/wiki/File%3ADiorama_Pembangunan_Candi_Jago.jpg) |
| `museum-tampak-depan.jpg` | Muhamad Abdu | CC BY-SA 4.0 | [File:Museum Mpu Purwa.jpg](https://commons.wikimedia.org/wiki/File%3AMuseum_Mpu_Purwa.jpg) |
| `halaman-museum.jpg` | Muhamad Abdu | CC BY-SA 4.0 | [File:Museum Mpu Purwa 1.jpg](https://commons.wikimedia.org/wiki/File%3AMuseum_Mpu_Purwa_1.jpg) |
| `ruang-koleksi-lantai-dua.jpg` | Farizor | CC BY 4.0 | [File:Koleksi Museum Lantai Dua.jpg](https://commons.wikimedia.org/wiki/File%3AKoleksi_Museum_Lantai_Dua.jpg) |

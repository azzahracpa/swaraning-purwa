# Swaraning Purwa

Website informasi dan edukasi digital **Museum Mpu Purwa Kota Malang**.
Project akhir mata kuliah Pengembangan Aplikasi Web Lanjut, FILKOM UB.

> Semua data di aplikasi ini adalah **data contoh** dan belum diverifikasi pihak museum.
> Situs ini bukan situs resmi Museum Mpu Purwa.

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

### Akun contoh dari seeder

| Peran | Email | Kata sandi |
| --- | --- | --- |
| Admin | admin@swaraning-purwa.test | `password` |
| User | pengguna@swaraning-purwa.test | `password` |
| User | pengguna2@swaraning-purwa.test | `password` |

Kata sandi bisa diganti lewat variabel `SEED_ADMIN_PASSWORD` dan `SEED_USER_PASSWORD`.
Untuk produksi wajib diganti dengan nilai kuat.

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
8. Teks antarmuka berbahasa Indonesia. Data contoh diberi label "contoh".

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
| Gambar contoh seeder | aset statis di `public/images/contoh/`, ikut di repo |
| Proxy dan HTTPS | `trustProxies(at: '*')` di `bootstrap/app.php`; kalau `APP_URL` diawali `https://`, semua URL dipaksa https |
| Aset Vite | dibangun saat build (`npm run build`), folder `public/build` tidak di-commit |
| Rahasia | tidak ada `.env` di repo; semua variabel ada di `.env.example` beserta penjelasannya |

Contoh menyimpan dan menampilkan upload:

```php
// simpan (controller)
$path = $request->file('foto')->store('koleksi');          // disk mengikuti FILESYSTEM_DISK
$koleksi->images()->create(['image_url' => $path, 'is_primary' => true]);

// tampilkan (model / view)
$image->url   // accessor memakai Storage::url(), gambar contoh memakai asset()
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
| Penanda data contoh | `<x-penanda-contoh />` |
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

Catatan T10: `UploadedFile::fake()` melaporkan tipe MIME dari **nama** berkas, sehingga berkas teks
bernama `.jpg` bisa lolos di tes padahal di aplikasi sungguhan ditolak (Laravel membaca isi berkas).
Karena itu tes T10 membuat berkas teks sungguhan untuk kasus tersebut.

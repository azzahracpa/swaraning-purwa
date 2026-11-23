# Deploy Swaraning Purwa ke Vercel

Aplikasi dijalankan sebagai **container** di Vercel memakai FrankenPHP (PHP 8.4 + Caddy).
Polanya mengikuti panduan resmi Vercel "Deploy Laravel on Vercel with Docker".

| Berkas | Fungsi |
| --- | --- |
| `Dockerfile.vercel` | Build 3 tahap: aset Vite (Node) → dependensi Composer → runtime FrankenPHP |
| `Caddyfile` | Hanya folder `public` yang terbuka, route lain diteruskan ke `public/index.php` |
| `vercel.json` | Mendaftarkan service container `app` dan meneruskan semua path ke sana |
| `.dockerignore` | Mencegah `.env`, `vendor`, `node_modules`, dan `tests` masuk ke image |
| `routes/modules/kesehatan.php` | `GET /health` untuk uji cepat setelah deploy (cek aplikasi + database) |

Server Vercel tidak punya disk permanen, jadi:

- **Database**: MySQL terkelola di luar Vercel.
- **Sesi dan cache**: tabel `sessions` dan `cache` di database (`SESSION_DRIVER=database`, `CACHE_STORE=database`).
- **Upload gambar**: penyimpanan S3 kompatibel (`FILESYSTEM_DISK=s3`).
- **Log**: `LOG_CHANNEL=stderr`, terbaca di menu **Logs** Vercel.

## 1. Siapkan MySQL gratis

Pilih salah satu. Ketentuan paket gratis bisa berubah, cek halaman harga penyedia sebelum mendaftar.

| Penyedia | Catatan |
| --- | --- |
| **TiDB Cloud** (Starter/Serverless) | Kompatibel MySQL, port `4000`, **wajib SSL** |
| **Aiven for MySQL** (Free plan) | MySQL asli, wajib SSL, ada batas ukuran penyimpanan |
| **Clever Cloud MySQL** (paket DEV) | Kecil, cukup untuk demo |

Catat: host, port, nama database, user, dan password. **Jangan** tulis nilai ini di repo, README, atau dokumen apa pun.

Untuk penyedia yang mewajibkan SSL, isi `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt`
(berkas sertifikat bawaan image Debian yang dipakai container).

## 2. Siapkan penyimpanan S3 kompatibel

Contoh dengan **Cloudflare R2** (ada kuota gratis):

1. Buat bucket, misal `swaraning-purwa`.
2. Aktifkan akses publik bucket (domain `r2.dev` atau domain sendiri). Ini menjadi `AWS_URL`.
3. Buat API token R2 dengan izin *Object Read & Write* untuk bucket itu → dapat Access Key ID dan Secret.

| Variabel | Contoh nilai (bukan asli) |
| --- | --- |
| `AWS_ACCESS_KEY_ID` | dari token R2 |
| `AWS_SECRET_ACCESS_KEY` | dari token R2 |
| `AWS_DEFAULT_REGION` | `auto` |
| `AWS_BUCKET` | `swaraning-purwa` |
| `AWS_ENDPOINT` | `https://<account-id>.r2.cloudflarestorage.com` |
| `AWS_URL` | `https://pub-xxxx.r2.dev` |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` |

Alternatif: Supabase Storage (punya endpoint S3) atau Backblaze B2. Aplikasi tidak memasang ACL per berkas,
jadi akses publik diatur dari pengaturan bucket di dasbor penyedia.

## 3. Buat APP_KEY

Jalankan di laptop (tidak mengubah `.env`):

```bash
php artisan key:generate --show
```

Buat **dua** kunci berbeda: satu untuk Production, satu untuk Preview. Simpan di pengelola kata sandi, bukan di repo.

## 4. Isi Environment Variables di Vercel

Vercel → Project → **Settings → Environment Variables**. Centang lingkungan yang sesuai.

| Variabel | Production | Preview |
| --- | --- | --- |
| `APP_KEY` | kunci produksi | kunci preview (berbeda) |
| `APP_ENV` | `production` | `production` |
| `APP_DEBUG` | `false` | `false` (boleh `true` sementara saat mencari galat) |
| `APP_URL` | `https://<nama-proyek>.vercel.app` | URL preview, atau biarkan sama |
| `DB_CONNECTION` | `mysql` | `mysql` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | database produksi | **database terpisah** untuk preview |
| `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` | sama |
| `SESSION_DRIVER` / `CACHE_STORE` | `database` | `database` |
| `SESSION_SECURE_COOKIE` | `true` | `true` |
| `QUEUE_CONNECTION` | `sync` | `sync` |
| `LOG_CHANNEL` | `stderr` | `stderr` |
| `FILESYSTEM_DISK` | `s3` | `s3` |
| `AWS_*` | bucket produksi | bucket/folder preview |

Setelah mengubah variabel, lakukan **Redeploy** supaya nilainya terbaca.

## 5. Jalankan migrasi dari laptop

Migrasi **tidak** dijalankan saat container menyala (banyak instance bisa menyala bersamaan).
Jalankan sekali dari laptop memakai berkas `.env.production` yang **tidak ikut di-commit** (sudah ada di `.gitignore`).

Isi `.env.production` minimal: `APP_KEY`, `DB_*`, `MYSQL_ATTR_SSL_CA` (di laptop pakai path sertifikat laptop,
atau kosongkan jika penyedia tidak mewajibkan), `SEED_ADMIN_PASSWORD`, dan `SEED_USER_PASSWORD` dengan kata sandi kuat.

Mac (Terminal):

```bash
cp .env.example .env.production
open -e .env.production
php artisan migrate --force --env=production
php artisan db:seed --force --env=production
```

Windows (PowerShell):

```powershell
Copy-Item .env.example .env.production
notepad .env.production
php artisan migrate --force --env=production
php artisan db:seed --force --env=production
```

Path sertifikat SSL di laptop: Mac `/etc/ssl/cert.pem`, Windows unduh `cacert.pem` dari situs curl
lalu isi path lengkapnya. Hapus `.env.production` dari laptop setelah selesai bila tidak dipakai lagi.

## 6. Deploy

1. Vercel → **Add New → Project** → impor repositori GitHub kelompok.
2. Root directory: `./` (tempat `vercel.json`). Framework preset: biarkan, `vercel.json` sudah menentukan container.
3. Klik **Deploy**. Setiap push ke `main` membuat deployment Production; branch lain membuat Preview.

## 7. Daftar uji setelah deploy

| Uji | Hasil yang diharapkan |
| --- | --- |
| Buka `/health` | `{"status":"ok","database":"ok"}` |
| Buka `/` | Beranda tampil dengan CSS dan font (aset Vite termuat) |
| Masuk sebagai admin, buka `/admin` | Dashboard pengelola |
| Tambah koleksi dengan foto | Foto tampil; alamat gambar mengarah ke `AWS_URL` |
| Daftar akun baru, ajukan reservasi | Kode booking muncul, status Menunggu |
| Setujui reservasi dari admin | Status Disetujui, sisa kursi berkurang |
| Kerjakan kuis | Skor tampil di halaman hasil |
| Muat ulang halaman setelah masuk | Tetap masuk (sesi tersimpan di database) |
| Buka dengan `http://` | Tautan dan form tetap `https://` |

## 8. Pemecahan masalah

| Gejala | Penyebab dan solusi |
| --- | --- |
| 502 Bad Gateway | FrankenPHP gagal menyala atau port salah. Pastikan `Caddyfile` memakai `:{$PORT:80}`. Lihat Logs. |
| "No application encryption key" | `APP_KEY` belum diisi untuk lingkungan itu. Isi lalu Redeploy. |
| `/health` membalas 503 `database: gagal` | `DB_*` salah, IP diblokir penyedia, atau SSL wajib tetapi `MYSQL_ATTR_SSL_CA` kosong. |
| 419 Page Expired setelah login | Tabel `sessions` belum ada (migrasi belum dijalankan) atau `SESSION_SECURE_COOKIE` tidak sesuai. |
| Halaman tanpa CSS | Build Vite gagal. Cek log tahap `aset` di build Vercel. |
| Link `http://` / mixed content | `APP_URL` belum diisi `https://...`. |
| Upload gagal 500 | Kredensial `AWS_*` salah atau endpoint keliru. Disk `s3` sengaja melempar galat agar terlihat di Logs. |
| Gambar contoh seeder tidak tampil | Gambar contoh ada di `public/images/contoh`, ikut ter-build. Pastikan seeder dijalankan ke database produksi. |

## Alternatif bila runtime container tidak tersedia

Jika akun Vercel tidak bisa memakai runtime container, gunakan runtime komunitas `vercel-php`:

1. Hapus `vercel.json` di atas dan buat `api/index.php` berisi `require __DIR__.'/../public/index.php';`.
2. Buat `vercel.json` baru dengan `"functions": {"api/index.php": {"runtime": "vercel-php@<versi>"}}`
   dan rewrite semua path ke `/api/index.php`. Versi runtime dan versi PHP-nya cek di repositori `vercel-community/php`.
3. Aset Vite harus di-build di Vercel (`"buildCommand": "npm run build"`) dan folder `storage`
   diarahkan ke `/tmp` karena sistem berkas hanya-baca.

Cara ini tidak dipakai secara default karena runtime komunitas tidak resmi dan versi PHP-nya bisa tertinggal.

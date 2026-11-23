@extends('layouts.admin')

@section('title', 'Kelola Profil Museum')
@section('halaman', 'Kelola Profil Museum')

@section('content')
    <form method="POST" action="{{ route('admin.profil-museum.update') }}" enctype="multipart/form-data"
          x-data="{
              nama: @js(old('name', $profil->name)),
              alamat: @js(old('address', $profil->address)),
              jam: @js(old('opening_hours', $profil->opening_hours)),
              telepon: @js(old('phone', $profil->phone)),
              email: @js(old('email', $profil->email)),
              get emailValid() { return ! this.email || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email) },
          }">
        @csrf
        @method('PUT')

        <x-admin.kepala judul="Profil" aksen="museum"
                        :deskripsi="'Satu profil untuk halaman Profil Museum, Beranda, dan footer.'.($profil->updated_at ? ' Terakhir diperbarui '.$profil->updated_at->translatedFormat('j M Y').'.' : '')">
            <a href="{{ route('profil-museum') }}" class="tombol tombol-garis" target="_blank">Lihat halaman Profil</a>
            <button type="submit" class="tombol tombol-utama">Simpan profil</button>
        </x-admin.kepala>

        @if ($errors->any())
            <x-peringatan jenis="galat" judul="Profil belum disimpan" :tutup="false" class="mb-6">
                {{ $errors->first() }}
            </x-peringatan>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
            <div class="flex flex-col gap-6">
                <x-admin.kotak nomor="01" label="Identitas">
                    <x-kolom-isian name="name" label="Nama museum" :value="$profil->name" wajib x-model="nama" />
                    <x-kolom-isian name="description" label="Deskripsi singkat" type="textarea" rows="3" :value="$profil->description"
                                   bantuan="Tampil di Beranda dan bagian atas halaman Profil." />
                    <div class="max-w-[420px]">
                        <x-unggah-gambar name="image" label="Gambar utama" :url-sekarang="$profil->gambar_url"
                                         bantuan="Opsional. JPG, PNG, atau WEBP, maksimal 2 MB. Dipakai sebagai latar kepala halaman Profil." />
                    </div>
                </x-admin.kotak>

                <x-admin.kotak nomor="02" label="Sejarah">
                    <x-kolom-isian name="history" label="Sejarah museum" type="textarea" rows="8" :value="$profil->history"
                                   bantuan="Pisahkan paragraf dengan baris kosong. Isi dengan narasi yang sudah diverifikasi." />
                </x-admin.kotak>

                <x-admin.kotak nomor="03" label="Kontak dan kunjungan">
                    <x-kolom-isian name="address" label="Alamat" :value="$profil->address" x-model="alamat" />
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-kolom-isian name="phone" label="Telepon" :value="$profil->phone" x-model="telepon" />
                        <div class="flex flex-col gap-2">
                            <x-kolom-isian name="email" label="Email" type="email" :value="$profil->email" x-model="email" />
                            <p class="kolom-pesan-galat" x-show="! emailValid" x-cloak>
                                <x-ikon nama="peringatan" class="size-4" /> Format email belum benar. Contoh: nama@domain.id
                            </p>
                        </div>
                    </div>
                    <x-kolom-isian name="opening_hours" label="Jam buka" :value="$profil->opening_hours" x-model="jam"
                                   bantuan="Teks bebas, ditampilkan apa adanya. Tulis hari libur bila ada." />
                </x-admin.kotak>
            </div>

            {{-- Pratinjau langsung, berubah saat isian diketik --}}
            <div class="flex flex-col gap-3 xl:sticky xl:top-24 xl:self-start">
                <div class="flex items-center justify-between">
                    <p class="text-label font-semibold">Pratinjau di situs</p>
                    <x-penanda-contoh teks="data contoh" />
                </div>
                <div class="overflow-hidden rounded-kartu bg-cokelat text-di-gelap">
                    <x-tempat-foto class="h-36 rounded-none" keterangan="Foto gedung museum" />
                    <div class="flex flex-col gap-3 p-5">
                        <p class="text-label-kecil font-semibold uppercase tracking-[0.1em] text-emas">Kunjungi</p>
                        <p class="teks-h3 text-ivory" x-text="nama || 'Nama museum'"></p>
                        <p class="flex gap-2 text-kecil"><x-ikon nama="lokasi" class="size-4 text-emas" /><span x-text="alamat || 'Alamat belum diisi'"></span></p>
                        <p class="flex gap-2 text-kecil"><x-ikon nama="jam" class="size-4 text-emas" /><span x-text="jam || 'Jam buka belum diisi'"></span></p>
                        <p class="flex gap-2 text-kecil"><x-ikon nama="telepon" class="size-4 text-emas" /><span x-text="telepon || 'Telepon belum diisi'"></span></p>
                        <p class="flex gap-2 text-kecil"><x-ikon nama="surat" class="size-4 text-emas" />
                            <span x-text="emailValid ? (email || 'Email belum diisi') : 'Email belum valid, tidak ditampilkan'" :class="! emailValid && 'opacity-60'"></span></p>
                    </div>
                </div>
                <p class="flex gap-2 text-kecil text-cokelat-600">
                    <x-ikon nama="info" class="size-4 shrink-0" />
                    Kolom yang kosong tidak ditampilkan di situs. Profil museum tidak bisa dihapus, hanya diubah.
                </p>
            </div>
        </div>
    </form>
@endsection

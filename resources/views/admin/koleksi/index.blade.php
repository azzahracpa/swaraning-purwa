@extends('layouts.admin')

@section('title', 'Kelola Koleksi')
@section('halaman', 'Kelola Koleksi')

@section('content')
    <x-admin.kepala judul="Kelola" aksen="koleksi"
                    deskripsi="Data koleksi, kategori, dan foto yang tampil di Katalog publik. Koleksi tanpa foto tetap tampil dengan penanda foto belum tersedia.">
        <a href="{{ route('admin.koleksi.create') }}" class="tombol tombol-utama">Tambah koleksi</a>
    </x-admin.kepala>

    <form method="GET" action="{{ route('admin.koleksi.index') }}" class="flex flex-col gap-3 pb-6 lg:flex-row lg:items-center">
        @if ($kategoriAktif)
            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
        @endif
        <x-admin.cari placeholder="Cari nama, periode, atau bahan" />
        <div class="flex flex-wrap gap-2">
            <x-admin.chip :href="route('admin.koleksi.index', ['cari' => $cari ?: null])" :aktif="! $kategoriAktif" :jumlah="$kategori->sum('collections_count')">Semua</x-admin.chip>
            @foreach ($kategori as $item)
                <x-admin.chip :href="route('admin.koleksi.index', ['kategori' => $item->id, 'cari' => $cari ?: null])"
                              :aktif="$kategoriAktif === $item->id" :jumlah="$item->collections_count">{{ $item->name }}</x-admin.chip>
            @endforeach
        </div>
        <label class="relative lg:ml-auto">
            <span class="sr-only">Urutkan</span>
            <x-ikon nama="urut" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
            <select name="urut" class="kolom-input h-10 pl-9 text-kecil" x-data x-on:change="$el.form.requestSubmit()">
                <option value="">Terakhir diubah</option>
                <option value="nama" @selected(request('urut') === 'nama')>Nama A–Z</option>
            </select>
        </label>
    </form>

    @error('pilih')
        <x-peringatan jenis="galat" class="mb-4">{{ $message }}</x-peringatan>
    @enderror

    @if ($koleksi->isEmpty())
        <x-keadaan-kosong judul="Koleksi tidak ditemukan" ikon="gambar">
            @if ($cari || $kategoriAktif) Coba kata kunci atau kategori lain. @else Belum ada koleksi. Tambahkan koleksi pertama. @endif
            <x-slot:aksi>
                <a href="{{ route('admin.koleksi.create') }}" class="tombol tombol-utama tombol-kecil">Tambah koleksi</a>
            </x-slot:aksi>
        </x-keadaan-kosong>
    @else
        {{-- Pilih beberapa koleksi lalu hapus atau pindah kategori sekaligus --}}
        <form method="POST" action="{{ route('admin.koleksi.aksi-massal') }}" id="form-massal"
              x-data="{
                  pilih: [],
                  semua: @js($koleksi->pluck('id')),
                  bukaKategori: false,
                  get semuaTerpilih() { return this.pilih.length === this.semua.length },
                  toggleSemua() { this.pilih = this.semuaTerpilih ? [] : [...this.semua] },
              }"
              x-on:submit="if ($event.submitter?.value === 'hapus') { $event.preventDefault(); $store.konfirmasi.buka($el, {
                  judul: 'Hapus ' + pilih.length + ' koleksi?',
                  isi: 'Foto dan favorit pengunjung untuk koleksi terpilih ikut terhapus.',
                  tombol: 'Hapus permanen',
              }); $el.querySelector('[name=aksi]').value = 'hapus' }">
            @csrf
            <input type="hidden" name="aksi" value="kategori">

            <div x-show="pilih.length" x-cloak x-transition
                 class="mb-4 flex flex-col gap-3 rounded-kartu bg-cokelat px-5 py-3 text-ivory sm:flex-row sm:items-center">
                <p class="text-label font-semibold"><span x-text="pilih.length"></span> koleksi dipilih</p>
                <div class="flex flex-wrap items-center gap-2 sm:ml-auto">
                    <button type="button" class="px-2 text-label font-semibold text-emas hover:underline" x-on:click="pilih = []; bukaKategori = false">Batalkan pilihan</button>
                    <template x-if="bukaKategori">
                        <div class="flex items-center gap-2">
                            <label for="kategori-massal" class="sr-only">Kategori tujuan</label>
                            <select id="kategori-massal" name="category_id" class="kolom-input h-10 text-kecil" required>
                                <option value="">Pilih kategori</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" name="aksi_tombol" value="kategori" class="tombol tombol-terang tombol-kecil"
                                    x-on:click="$el.form.querySelector('[name=aksi]').value = 'kategori'">Pindahkan</button>
                        </div>
                    </template>
                    <button type="button" class="tombol tombol-terang tombol-kecil" x-show="! bukaKategori" x-on:click="bukaKategori = true">Ubah kategori</button>
                    <button type="submit" value="hapus" class="tombol tombol-bahaya tombol-kecil" formnovalidate>Hapus terpilih</button>
                </div>
            </div>

            <div class="kartu overflow-x-auto">
                <table class="tabel min-w-[900px]">
                    <thead class="bg-lembut">
                        <tr>
                            <th class="w-10">
                                <input type="checkbox" class="rounded-kecil border-garis-kuat text-maroon focus:ring-maroon"
                                       :checked="semuaTerpilih" x-on:change="toggleSemua()" aria-label="Pilih semua koleksi di halaman ini">
                            </th>
                            <th>Koleksi</th>
                            <th>Kategori</th>
                            <th>Periode · bahan</th>
                            <th>Foto</th>
                            <th>Diubah</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($koleksi as $item)
                            <tr :class="pilih.includes({{ $item->id }}) && 'bg-maroon-lembut hover:bg-maroon-lembut'">
                                <td>
                                    <input type="checkbox" name="pilih[]" value="{{ $item->id }}" x-model.number="pilih"
                                           class="rounded-kecil border-garis-kuat text-maroon focus:ring-maroon" aria-label="Pilih {{ $item->name }}">
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-input bg-foto">
                                            @if ($item->primaryImage)
                                                <img src="{{ $item->primaryImage->url }}" alt="" class="size-full object-cover" loading="lazy">
                                            @else
                                                <x-ikon nama="gambar" class="text-emas-700" />
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold">{{ $item->name }}</p>
                                            <p class="text-cokelat-600">{{ $item->location ? 'Lokasi: '.$item->location : 'Lokasi belum diisi' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><x-label-kategori>{{ $item->category->name }}</x-label-kategori></td>
                                <td>{{ $item->metaSingkat() ?: '—' }}</td>
                                <td>
                                    @if ($item->images_count)
                                        {{ $item->images_count }} foto
                                    @else
                                        <span class="flex items-center gap-1 font-semibold text-status-ditolak-teks"><x-ikon nama="peringatan" class="size-4" /> Belum ada</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-cokelat-600">{{ $item->updated_at->translatedFormat('j M Y') }}</td>
                                <td>
                                    <div class="flex justify-end">
                                        <a href="{{ route('admin.koleksi.edit', $item) }}" class="tombol tombol-garis tombol-kecil">Ubah</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-6">{{ $koleksi->links() }}</div>
    @endif
@endsection

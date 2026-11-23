@extends('layouts.admin')

@section('title', 'Kelola Kategori')
@section('halaman', 'Kelola Kategori')

@section('content')
    <x-admin.kepala judul="Kelola" aksen="kategori" deskripsi="Kategori mengelompokkan koleksi di Katalog. Nama kategori harus unik.">
        <a href="{{ route('admin.kategori.index') }}#form-kategori" class="tombol tombol-utama">Tambah kategori</a>
    </x-admin.kepala>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div>
            <p class="mb-4 flex items-center gap-3 text-label font-semibold">
                {{ $kategori->count() }} kategori
            </p>

            @if ($kategori->isEmpty())
                <x-keadaan-kosong judul="Belum ada kategori" ikon="menu">
                    Tambahkan kategori pertama, misalnya Arca atau Prasasti, lewat form di samping.
                </x-keadaan-kosong>
            @else
                <div class="kartu overflow-x-auto">
                    <table class="tabel min-w-[620px]">
                        <thead class="bg-lembut">
                            <tr><th>Nama</th><th>Deskripsi</th><th>Dipakai oleh</th><th class="text-right">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $item)
                                <tr class="{{ $sedangDiubah?->id === $item->id ? 'bg-maroon-lembut' : '' }}">
                                    <td class="font-semibold">{{ $item->name }}</td>
                                    <td class="text-cokelat-600">{{ $item->description ?: 'Belum ada deskripsi.' }}</td>
                                    <td>{{ $item->collections_count ? $item->collections_count.' koleksi' : 'Belum dipakai' }}</td>
                                    <td>
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.kategori.index', ['ubah' => $item->id]) }}#form-kategori" class="tombol tombol-garis tombol-kecil">Ubah</a>
                                            @if ($item->collections_count)
                                                <span class="tombol-ikon size-10 cursor-not-allowed opacity-40" title="Masih dipakai koleksi" aria-label="Tidak bisa dihapus, masih dipakai koleksi">
                                                    <x-ikon nama="tutup" class="size-4" />
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('admin.kategori.destroy', $item) }}" x-data
                                                      x-on:submit.prevent="$store.konfirmasi.buka($el, {
                                                          judul: 'Hapus kategori ini?',
                                                          isi: 'Kategori belum dipakai koleksi mana pun.',
                                                          ringkasan: @js($item->name),
                                                          tombol: 'Hapus kategori',
                                                      })">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="tombol-ikon size-10 text-status-ditolak-teks" aria-label="Hapus {{ $item->name }}">
                                                        <x-ikon nama="tutup" class="size-4" />
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <p class="mt-4 flex gap-2 text-kecil text-cokelat-600">
                <x-ikon nama="kunci" class="size-4 shrink-0" />
                Kategori yang masih dipakai koleksi tidak dapat dihapus. Pindahkan koleksinya ke kategori lain lebih dulu.
            </p>
        </div>

        {{-- Form tambah / ubah kategori --}}
        <div id="form-kategori" class="scroll-mt-24">
            <form method="POST"
                  action="{{ $sedangDiubah ? route('admin.kategori.update', $sedangDiubah) : route('admin.kategori.store') }}"
                  class="kartu flex flex-col gap-5 p-6 xl:sticky xl:top-24">
                @csrf
                @if ($sedangDiubah)
                    @method('PUT')
                @endif
                <div>
                    <p class="teks-label-kecil uppercase tracking-[0.1em] text-cokelat-600">{{ $sedangDiubah ? 'Ubah kategori' : 'Kategori baru' }}</p>
                    <h2 class="teks-h3 mt-1">{{ $sedangDiubah ? $sedangDiubah->name : 'Tambah kategori' }}</h2>
                </div>
                <x-kolom-isian name="name" label="Nama kategori" :value="$sedangDiubah?->name" wajib maxlength="100" />
                <x-kolom-isian name="description" label="Deskripsi" type="textarea" rows="3" :value="$sedangDiubah?->description"
                               placeholder="Penjelasan singkat (opsional)" bantuan="Tampil di atas daftar koleksi saat kategori dipilih." />
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.kategori.index') }}" class="tombol tombol-garis">Batal</a>
                    <button type="submit" class="tombol tombol-utama">Simpan kategori</button>
                </div>
            </form>
        </div>
    </div>
@endsection

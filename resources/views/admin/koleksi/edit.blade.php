@extends('layouts.admin')

@section('title', 'Ubah Koleksi')
@section('halaman', 'Kelola Koleksi › '.$koleksi->name)

@php $maks = \App\Http\Requests\Admin\StoreKoleksiRequest::MAKS_FOTO; @endphp

@section('content')
    <x-admin.kepala judul="Ubah" aksen="koleksi"
                    :deskripsi="$koleksi->name.' · terakhir diubah '.$koleksi->updated_at->translatedFormat('j M Y').'. Kolom bertanda * wajib diisi.'">
        <a href="{{ route('koleksi.show', $koleksi) }}" class="tombol tombol-garis" target="_blank">Lihat di Katalog</a>
        <button type="submit" form="form-koleksi" class="tombol tombol-utama">Simpan perubahan</button>
    </x-admin.kepala>

    @if ($errors->hasAny(['name', 'category_id', 'description', 'historical_period', 'origin', 'material', 'location']))
        <x-peringatan jenis="galat" judul="Koleksi belum disimpan" :tutup="false" class="mb-6">
            Periksa isian yang ditandai, lalu simpan lagi.
        </x-peringatan>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="flex flex-col gap-6">
            <form method="POST" action="{{ route('admin.koleksi.update', $koleksi) }}" id="form-koleksi"
                  x-data="{ berubah: false }" x-on:input="berubah = true">
                @csrf
                @method('PUT')
                @include('admin.koleksi._form')

                <div class="kartu mt-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-end">
                    <p class="flex items-center gap-2 text-kecil text-cokelat-600 sm:mr-auto" x-show="berubah" x-cloak x-transition>
                        <x-ikon nama="info" class="size-4" /> Ada perubahan yang belum disimpan.
                    </p>
                    <a href="{{ route('admin.koleksi.index') }}" class="tombol tombol-garis">Batal</a>
                    <button type="submit" class="tombol tombol-utama">Simpan perubahan</button>
                </div>
            </form>

            <div class="kartu flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-label font-semibold">Hapus koleksi ini</p>
                    <p class="text-kecil text-cokelat-600">Foto dan data favorit pengunjung untuk koleksi ini ikut terhapus.</p>
                </div>
                <form method="POST" action="{{ route('admin.koleksi.destroy', $koleksi) }}" x-data
                      x-on:submit.prevent="$store.konfirmasi.buka($el, {
                          judul: 'Hapus koleksi ini?',
                          isi: 'Foto dan favorit pengunjung untuk koleksi ini ikut terhapus dan tidak bisa dikembalikan.',
                          ringkasan: @js($koleksi->name.' · '.$koleksi->images->count().' foto'),
                          tombol: 'Hapus permanen',
                      })">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="tombol tombol-bahaya tombol-kecil">Hapus koleksi</button>
                </form>
            </div>
        </div>

        {{-- Panel foto koleksi --}}
        <x-admin.kotak nomor="02" label="Foto koleksi" class="self-start">
            <x-slot:kanan><span class="text-kecil text-cokelat-600">{{ $koleksi->images->count() }} dari {{ $maks }}</span></x-slot:kanan>

            @if ($koleksi->images->count() < $maks)
                <form method="POST" action="{{ route('admin.koleksi.foto.store', $koleksi) }}" enctype="multipart/form-data" class="flex flex-col gap-3"
                      x-data="{ mengirim: false }" x-on:submit="mengirim = true">
                    @csrf
                    <x-admin.unggah-banyak name="foto" :maks="$maks" :sisa="$maks - $koleksi->images->count()" />
                    <button type="submit" class="tombol tombol-garis tombol-kecil self-start" :disabled="mengirim">
                        <span x-show="! mengirim">Unggah foto</span><span x-show="mengirim" x-cloak>Mengunggah…</span>
                    </button>
                </form>
            @else
                <p class="text-kecil text-cokelat-600">Batas {{ $maks }} foto tercapai. Hapus foto lama untuk menambah foto baru.</p>
            @endif

            @if ($koleksi->images->isEmpty())
                <p class="flex items-center gap-2 text-kecil text-status-ditolak-teks">
                    <x-ikon nama="peringatan" class="size-4" /> Belum ada foto. Koleksi tetap tampil dengan penanda foto belum tersedia.
                </p>
            @endif

            <div class="flex flex-col gap-4" x-data="{ terpilih: {{ $koleksi->images->first()?->id ?? 'null' }} }">
                @foreach ($koleksi->images as $foto)
                    <div class="flex flex-col gap-2">
                        <button type="button" x-on:click="terpilih = {{ $foto->id }}"
                                class="relative aspect-[16/10] overflow-hidden rounded-kartu bg-foto ring-offset-2 ring-offset-putih transition"
                                :class="terpilih === {{ $foto->id }} ? 'ring-2 ring-maroon' : 'hover:ring-1 hover:ring-garis-kuat'"
                                aria-label="Pilih foto {{ $loop->iteration }}">
                            <img src="{{ $foto->url }}" alt="{{ $foto->caption }}" class="size-full object-cover" loading="lazy">
                            @if ($foto->caption)
                                <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-cokelat/80 to-transparent p-3 text-left text-kecil text-ivory">{{ $foto->caption }}</span>
                            @endif
                        </button>
                        <div class="flex items-center justify-between gap-2">
                            @if ($foto->is_primary)
                                <x-badge-keadaan nada="menunggu" class="gap-1.5"><x-ikon nama="centang" class="size-3.5" /> Foto utama</x-badge-keadaan>
                            @else
                                <form method="POST" action="{{ route('admin.koleksi.foto.update', [$koleksi, $foto]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="utama" value="1">
                                    <button type="submit" class="text-label font-semibold text-maroon hover:underline">Jadikan utama</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.koleksi.foto.destroy', [$koleksi, $foto]) }}" x-data
                                  x-on:submit.prevent="$store.konfirmasi.buka($el, {
                                      judul: 'Hapus foto ini?',
                                      isi: @js($foto->is_primary ? 'Ini foto utama. Foto berikutnya akan menjadi foto utama.' : 'Foto dihapus dari koleksi dan penyimpanan.'),
                                      tombol: 'Hapus foto',
                                  })">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="tombol-ikon size-9 text-status-ditolak-teks" aria-label="Hapus foto {{ $loop->iteration }}">
                                    <x-ikon nama="tutup" class="size-4" />
                                </button>
                            </form>
                        </div>

                        {{-- Keterangan foto terpilih --}}
                        <form method="POST" action="{{ route('admin.koleksi.foto.update', [$koleksi, $foto]) }}"
                              x-show="terpilih === {{ $foto->id }}" x-cloak x-transition class="flex flex-col gap-2 rounded-input bg-lembut p-3">
                            @csrf
                            @method('PATCH')
                            <label for="caption-{{ $foto->id }}" class="kolom-label">Keterangan foto terpilih</label>
                            <div class="flex gap-2">
                                <input id="caption-{{ $foto->id }}" name="caption" value="{{ $foto->caption }}" maxlength="255"
                                       class="kolom-input h-10 flex-1 text-kecil" placeholder="Tampak depan arca">
                                <button type="submit" class="tombol tombol-garis tombol-kecil">Simpan</button>
                            </div>
                            <p class="kolom-bantuan">Ditampilkan di bawah foto pada halaman detail.</p>
                        </form>
                    </div>
                @endforeach
            </div>
        </x-admin.kotak>
    </div>
@endsection

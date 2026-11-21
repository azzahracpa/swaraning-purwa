@extends('layouts.admin')

@section('title', 'Kelola Artikel')
@section('halaman', 'Kelola Artikel')

@php
    $labelStatus = [
        'terbit' => ['Terbit', 'positif', 'centang'],
        'terjadwal' => ['Terjadwal', 'menunggu', 'edit'],
        'draf' => ['Draf', 'netral', 'larangan'],
    ];
@endphp

@section('content')
    <x-admin.kepala judul="Kelola" aksen="artikel"
                    deskripsi="Artikel edukasi yang tampil di halaman Artikel publik. Draf tidak terlihat oleh pengunjung sampai diterbitkan.">
        <a href="{{ route('admin.artikel.create') }}" class="tombol tombol-utama">Tulis artikel</a>
    </x-admin.kepala>

    <form method="GET" action="{{ route('admin.artikel.index') }}" class="flex flex-col gap-3 pb-6 lg:flex-row lg:items-center">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <x-admin.cari placeholder="Cari judul artikel" />
        <div class="flex flex-wrap gap-2">
            <x-admin.chip :href="route('admin.artikel.index', ['cari' => $cari ?: null])" :aktif="! $status" :jumlah="$jumlah['semua']">Semua</x-admin.chip>
            <x-admin.chip :href="route('admin.artikel.index', ['status' => 'terbit', 'cari' => $cari ?: null])" :aktif="$status === 'terbit'" :jumlah="$jumlah['terbit']">Terbit</x-admin.chip>
            <x-admin.chip :href="route('admin.artikel.index', ['status' => 'terjadwal', 'cari' => $cari ?: null])" :aktif="$status === 'terjadwal'" :jumlah="$jumlah['terjadwal']">Terjadwal</x-admin.chip>
            <x-admin.chip :href="route('admin.artikel.index', ['status' => 'draf', 'cari' => $cari ?: null])" :aktif="$status === 'draf'" :jumlah="$jumlah['draf']">Draf</x-admin.chip>
        </div>
        <label class="relative lg:ml-auto">
            <span class="sr-only">Urutkan</span>
            <x-ikon nama="urut" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
            <select name="urut" class="kolom-input h-10 pl-9 text-kecil" x-data x-on:change="$el.form.requestSubmit()">
                <option value="">Terakhir diubah</option>
                <option value="judul" @selected(request('urut') === 'judul')>Judul A–Z</option>
            </select>
        </label>
    </form>

    @if ($artikel->isEmpty())
        <x-keadaan-kosong judul="Belum ada artikel" ikon="edit">
            @if ($cari || $status) Tidak ada artikel yang cocok dengan filter. @else Mulai dengan menulis artikel pertama. @endif
            <x-slot:aksi>
                <a href="{{ route('admin.artikel.create') }}" class="tombol tombol-utama tombol-kecil">Tulis artikel</a>
            </x-slot:aksi>
        </x-keadaan-kosong>
    @else
        <div class="kartu overflow-x-auto">
            <table class="tabel min-w-[820px]">
                <thead class="bg-lembut">
                    <tr>
                        <th>Artikel</th>
                        <th>Penulis</th>
                        <th>Status</th>
                        <th>Tanggal terbit</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($artikel as $item)
                        @php [$label, $nada, $ikon] = $labelStatus[$item->status()]; @endphp
                        <tr>
                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="size-14 shrink-0 overflow-hidden rounded-input bg-cokelat">
                                        @if ($item->gambar_url)
                                            <img src="{{ $item->gambar_url }}" alt="" class="size-full object-cover" loading="lazy">
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold">{{ $item->title }}</p>
                                        <p class="line-clamp-1 text-cokelat-600">{{ $item->ringkasan(70) ?: 'Belum ada ringkasan' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->author->name }}</td>
                            <td>
                                <x-badge-keadaan :nada="$nada" class="gap-1.5"><x-ikon :nama="$ikon" class="size-3.5" />{{ $label }}</x-badge-keadaan>
                            </td>
                            <td class="whitespace-nowrap">
                                @if ($item->published_at)
                                    {{ $item->published_at->translatedFormat('j M Y') }}<br>
                                    <span class="text-cokelat-600">{{ $item->published_at->format('H.i') }}</span>
                                @else
                                    —<br><span class="text-cokelat-600">diubah {{ $item->updated_at->translatedFormat('j M') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.artikel.edit', $item) }}" class="tombol tombol-garis tombol-kecil">Ubah</a>
                                    <form method="POST" action="{{ route('admin.artikel.destroy', $item) }}" x-data
                                          x-on:submit.prevent="$store.konfirmasi.buka($el, {
                                              judul: 'Hapus artikel ini?',
                                              isi: 'Artikel dan gambar sampulnya akan dihapus permanen.',
                                              ringkasan: @js($item->title),
                                              tombol: 'Hapus permanen',
                                          })">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tombol-ikon size-10 text-status-ditolak-teks" aria-label="Hapus {{ $item->title }}">
                                            <x-ikon nama="tutup" class="size-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $artikel->links() }}</div>
    @endif
@endsection

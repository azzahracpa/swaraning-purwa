@extends('layouts.publik')

@section('title', 'Katalog Koleksi')

@php
    $namaKategoriAktif = $kategoriAktif ? $kategori->firstWhere('id', $kategoriAktif)?->name : null;
@endphp

@section('content')
    <section class="wadah pt-6">
        <x-breadcrumb :items="['Beranda' => route('beranda'), 'Katalog Koleksi' => null]" />

        <div class="mt-8 grid gap-4 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-end lg:gap-16">
            <h1 class="teks-display-l uppercase" x-data x-muncul>Katalog <em class="aksen-miring">koleksi</em></h1>
            <p class="hidden text-isi text-cokelat-600 sm:block lg:pb-2" x-data x-muncul="120">
                Cari berdasarkan nama atau saring per kategori. Buka kartu untuk melihat galeri foto dan keterangan lengkap.
            </p>
        </div>

        {{--
            Form pencarian (GET). Saat mengetik, form dikirim otomatis setelah jeda 500 ms.
            Tombol kategori juga langsung mengirim form.
        --}}
        <form method="GET" action="{{ route('koleksi.index') }}" role="search"
              x-data="{ cari: @js($cari), memuat: false, kirim() { this.memuat = true; this.$root.submit(); } }"
              x-on:submit="memuat = true"
              class="mt-8 flex flex-col gap-4 border-b border-garis pb-6 lg:flex-row lg:items-center lg:gap-5">
            {{-- Kategori aktif ikut terkirim saat mencari. Tombol kategori di bawah menimpanya. --}}
            <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
            <div class="relative w-full lg:max-w-[430px]">
                <label for="cari" class="sr-only">Cari nama koleksi</label>
                <x-ikon nama="cari" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-cokelat-600" />
                <input id="cari" type="search" name="cari" x-model="cari"
                       x-on:input.debounce.500ms="kirim()"
                       x-init="if (cari) { $el.focus(); $el.setSelectionRange(cari.length, cari.length) }"
                       placeholder="Cari nama koleksi, misalnya arca"
                       maxlength="100"
                       class="kolom-input pl-11 pr-11 [&::-webkit-search-cancel-button]:hidden">
                <button type="button" x-show="cari" x-cloak
                        x-on:click="cari = ''; $nextTick(() => kirim())"
                        class="absolute right-2 top-1/2 flex size-8 -translate-y-1/2 items-center justify-center rounded-kecil text-cokelat-600 transition hover:bg-lembut hover:text-cokelat"
                        aria-label="Hapus kata pencarian">
                    <x-ikon nama="tutup" class="size-4" />
                </button>
            </div>

            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori">
                <button type="submit" name="kategori" value=""
                        class="tombol tombol-kecil {{ $kategoriAktif ? 'border border-garis-kuat bg-lembut text-cokelat hover:border-cokelat' : 'bg-cokelat text-ivory' }}"
                        @if (! $kategoriAktif) aria-pressed="true" @endif>
                    Semua
                </button>
                @foreach ($kategori as $item)
                    <button type="submit" name="kategori" value="{{ $item->id }}"
                            class="tombol tombol-kecil {{ $kategoriAktif === $item->id ? 'bg-cokelat text-ivory' : 'border border-garis-kuat bg-lembut text-cokelat hover:border-cokelat' }}"
                            @if ($kategoriAktif === $item->id) aria-pressed="true" @endif>
                        {{ $item->name }}
                    </button>
                @endforeach
            </div>

            <p class="text-kecil text-cokelat-600 lg:ml-auto" aria-live="polite">
                <span x-show="memuat" x-cloak>Memuat…</span>
                <span x-show="! memuat">Menampilkan {{ $koleksi->total() }} koleksi</span>
            </p>
        </form>
        @error('kategori')
            <p class="kolom-pesan-galat mt-3"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
        @enderror
    </section>

    <section class="wadah pb-24 pt-10 lg:pb-32 lg:pt-12">
        @if ($koleksi->isEmpty())
            {{-- Keadaan kosong sesuai desain: ilustrasi relung + saran --}}
            <div class="mx-auto flex max-w-[420px] flex-col items-center gap-5 py-10 text-center">
                <svg width="92" height="92" viewBox="0 0 92 92" fill="none" aria-hidden="true">
                    <path d="M14 90V44a32 32 0 0 1 64 0v46" stroke="#C6A15B" stroke-width="2" />
                    <path d="M26 90V46a20 20 0 0 1 40 0v44" stroke="#702F36" stroke-width="1.5" stroke-dasharray="3 4" />
                    <path d="M0 90h92" stroke="#3B2927" stroke-width="2" />
                </svg>
                <h2 class="teks-h2">Koleksi tidak ditemukan</h2>
                <p class="text-isi text-cokelat-600">
                    Tidak ada hasil
                    @if ($cari) untuk “{{ $cari }}” @endif
                    @if ($namaKategoriAktif) di kategori {{ $namaKategoriAktif }} @endif.
                    Coba kata kunci lain atau tampilkan semua kategori.
                </p>
                <a href="{{ route('koleksi.index') }}" class="tombol tombol-garis">Tampilkan semua kategori</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($koleksi as $item)
                    <div x-data x-muncul="{{ ($loop->index % 4) * 90 }}">
                        <x-kartu-koleksi :koleksi="$item" x-data x-miring="4" />
                    </div>
                @endforeach
            </div>

            <div class="mt-16 flex justify-center">
                {{ $koleksi->links() }}
            </div>
        @endif
    </section>
@endsection

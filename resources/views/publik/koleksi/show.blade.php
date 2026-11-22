@extends('layouts.publik')

@section('title', $koleksi->name)
@section('deskripsi', \Illuminate\Support\Str::limit($koleksi->description, 150))

@php
    // Data galeri untuk Alpine: url dan keterangan setiap foto (foto utama di depan)
    $galeri = $koleksi->images->map(fn ($foto) => [
        'url' => $foto->url,
        'keterangan' => $foto->caption ?? $koleksi->name,
    ])->values();

    $paragraf = collect(preg_split('/\R\s*\R/', trim($koleksi->description)))->filter()->values();

    $keterangan = [
        'Kategori' => $koleksi->category->name,
        'Periode' => $koleksi->historical_period,
        'Asal' => $koleksi->origin,
        'Bahan' => $koleksi->material,
        'Lokasi di museum' => $koleksi->location,
    ];
@endphp

@section('content')
    <section class="wadah pt-6">
        <x-breadcrumb :items="['Beranda' => route('beranda'), 'Katalog Koleksi' => route('koleksi.index'), $koleksi->name => null]" />

        <div class="mt-8 grid gap-10 lg:grid-cols-[minmax(0,600px)_minmax(0,1fr)] lg:gap-20">
            {{-- Galeri: klik thumbnail atau pakai tombol panah kiri/kanan; klik foto untuk memperbesar --}}
            <div x-data="{
                    foto: @js($galeri),
                    aktif: 0,
                    besar: false,
                    geser(arah) { if (this.foto.length) this.aktif = (this.aktif + arah + this.foto.length) % this.foto.length },
                 }"
                 x-on:keydown.left="geser(-1)" x-on:keydown.right="geser(1)">
                <div class="relative">
                    @if ($galeri->isEmpty())
                        <x-tempat-foto nada="lembut" class="aspect-[600/740] w-full rounded-relung" keterangan="Foto utama koleksi" />
                    @else
                        <button type="button" x-on:click="besar = true"
                                class="group relative block aspect-[600/740] w-full overflow-hidden rounded-relung bg-foto focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cokelat focus-visible:ring-offset-4"
                                aria-label="Perbesar foto">
                            <template x-for="(item, i) in foto" :key="i">
                                <img :src="item.url" :alt="item.keterangan"
                                     x-show="aktif === i"
                                     x-transition:enter="transition duration-300 ease-out"
                                     x-transition:enter-start="opacity-0 scale-[1.02]"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute inset-0 size-full object-cover">
                            </template>
                            <span class="absolute bottom-4 right-4 flex items-center gap-2 rounded-kecil bg-cokelat/80 px-3 py-1.5 text-label-kecil font-semibold text-ivory opacity-0 transition group-hover:opacity-100">
                                <x-ikon nama="mata" class="size-4" /> Perbesar
                            </span>
                        </button>

                        @if ($galeri->count() > 1)
                            <div class="pointer-events-none absolute inset-x-3 top-1/2 flex -translate-y-1/2 justify-between">
                                <button type="button" x-on:click="geser(-1)" class="tombol-ikon pointer-events-auto border-transparent bg-putih/80 backdrop-blur" aria-label="Foto sebelumnya">
                                    <x-ikon nama="panah-kiri" />
                                </button>
                                <button type="button" x-on:click="geser(1)" class="tombol-ikon pointer-events-auto border-transparent bg-putih/80 backdrop-blur" aria-label="Foto berikutnya">
                                    <x-ikon nama="panah-kanan" />
                                </button>
                            </div>
                        @endif
                    @endif
                </div>

                @if ($galeri->count() > 1)
                    <div x-geser class="mt-3 flex gap-3 overflow-x-auto pb-1" role="tablist" aria-label="Pilih foto">
                        <template x-for="(item, i) in foto" :key="'t' + i">
                            <button type="button" role="tab" :aria-selected="aktif === i"
                                    x-on:click="aktif = i"
                                    class="relative size-[90px] shrink-0 overflow-hidden rounded-input bg-foto ring-offset-2 ring-offset-ivory transition sm:size-[140px]"
                                    :class="aktif === i ? 'ring-2 ring-maroon' : 'opacity-75 hover:opacity-100'">
                                <img :src="item.url" alt="" class="size-full object-cover">
                            </button>
                        </template>
                    </div>
                @endif

                <p class="mt-3 text-kecil text-cokelat-600">
                    @if ($galeri->isNotEmpty())
                        Foto <span x-text="aktif + 1"></span> dari {{ $galeri->count() }}.
                        <span x-text="foto[aktif].keterangan"></span>
                    @else
                        Foto koleksi belum tersedia.
                    @endif
                </p>

                {{-- Tampilan foto besar --}}
                @if ($galeri->isNotEmpty())
                    <div x-show="besar" x-cloak x-transition.opacity
                         x-on:keydown.escape.window="besar = false"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-cokelat/95 p-4"
                         role="dialog" aria-modal="true" aria-label="Foto koleksi diperbesar">
                        <button type="button" x-on:click="besar = false" class="tombol absolute right-4 top-4 h-11 gap-2 border border-emas px-3.5 text-label text-ivory hover:bg-ivory/10">
                            <x-ikon nama="tutup" /> Tutup
                        </button>
                        <img :src="foto[aktif].url" :alt="foto[aktif].keterangan" x-trap.noscroll="besar"
                             class="max-h-[85vh] max-w-full rounded-kartu object-contain" tabindex="-1">
                        @if ($galeri->count() > 1)
                            <button type="button" x-on:click="geser(-1)" class="tombol-ikon absolute left-4 top-1/2 -translate-y-1/2 border-emas text-ivory hover:bg-ivory/10" aria-label="Foto sebelumnya">
                                <x-ikon nama="panah-kiri" />
                            </button>
                            <button type="button" x-on:click="geser(1)" class="tombol-ikon absolute right-4 top-1/2 -translate-y-1/2 border-emas text-ivory hover:bg-ivory/10" aria-label="Foto berikutnya">
                                <x-ikon nama="panah-kanan" />
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Keterangan koleksi --}}
            <div class="flex flex-col gap-6 lg:pt-6" x-data x-muncul="100">
                <div class="flex flex-wrap items-center gap-2">
                    <x-label-kategori>{{ $koleksi->category->name }}</x-label-kategori>
                    <x-penanda-contoh teks="nama dan data contoh" />
                </div>
                <h1 class="teks-display-l uppercase">{{ $koleksi->name }}</h1>
                <p class="text-isi text-cokelat-600">{{ \Illuminate\Support\Str::limit($paragraf->first(), 160) }}</p>

                {{-- Tombol favorit: simpan / hapus dari favorit (hanya untuk pengguna, bukan admin) --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
                    @auth
                        @if (! auth()->user()->isAdmin())
                            @if ($sudahFavorit)
                                <form method="POST" action="{{ route('favorit.destroy', $koleksi) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tombol tombol-garis group border-maroon text-maroon hover:bg-maroon hover:text-putih" aria-pressed="true">
                                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 17.2 3.4 10.6a3.9 3.9 0 0 1 5.5-5.5L10 6.2l1.1-1.1a3.9 3.9 0 0 1 5.5 5.5Z"/></svg>
                                        <span class="group-hover:hidden">Tersimpan di favorit</span>
                                        <span class="hidden group-hover:inline">Hapus dari favorit</span>
                                    </button>
                                </form>
                                <a href="{{ route('favorit.index') }}" class="tautan">Lihat koleksi favorit</a>
                            @else
                                <form method="POST" action="{{ route('favorit.store') }}" x-data="{ kirim: false }" x-on:submit="kirim = true">
                                    @csrf
                                    <input type="hidden" name="collection_id" value="{{ $koleksi->id }}">
                                    <button type="submit" class="tombol tombol-garis" :class="kirim && 'scale-95'">
                                        <x-ikon nama="hati" /> Simpan ke favorit
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="tombol tombol-garis">
                            <x-ikon nama="hati" /> Simpan ke favorit
                        </a>
                        <p class="flex items-center gap-1.5 text-kecil text-cokelat-600">
                            <x-ikon nama="kunci" class="size-4" /> Masuk untuk menyimpan favorit
                        </p>
                    @endauth
                </div>

                <div>
                    <div class="flex items-center justify-between border-b border-garis pb-3">
                        <h2 class="text-label font-semibold">Keterangan</h2>
                        <x-penanda-contoh teks="data contoh" />
                    </div>
                    <dl>
                        @foreach ($keterangan as $label => $nilai)
                            <div class="grid grid-cols-[140px_minmax(0,1fr)] gap-4 border-b border-garis py-3 text-kecil">
                                <dt class="text-cokelat-600">{{ $label }}</dt>
                                <dd class="text-cokelat">{{ $nilai ?: '–' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </section>

    {{-- 01 Deskripsi --}}
    <section class="wadah grid gap-6 pt-20 lg:grid-cols-[304px_minmax(0,720px)] lg:gap-10 lg:pt-32">
        <x-label-seksi nomor="01" label="Deskripsi" class="lg:pt-3" />
        <div class="flex flex-col gap-5" x-data x-muncul>
            <h2 class="teks-h1">Tentang koleksi ini</h2>
            @foreach ($paragraf as $isi)
                <p class="{{ $loop->first ? 'text-isi text-cokelat' : 'text-isi text-cokelat-600' }}">{{ $isi }}</p>
            @endforeach
        </div>
    </section>

    {{-- 02 Koleksi lain di kategori yang sama --}}
    <section class="wadah pb-24 pt-20 lg:pb-32 lg:pt-32">
        <div class="grid gap-6 border-b border-garis pb-8 lg:grid-cols-[304px_minmax(0,1fr)] lg:items-end lg:gap-10">
            <x-label-seksi nomor="02" label="Koleksi lain" class="lg:pb-3" />
            <h2 class="teks-display-l uppercase" x-data x-muncul>
                Masih dalam kategori <em class="aksen-miring">{{ $koleksi->category->name }}</em>
            </h2>
        </div>

        <div x-data x-geser class="-mx-5 mt-10 flex gap-6 overflow-x-auto px-5 pb-2 md:-mx-10 md:px-10 lg:mx-0 lg:grid lg:grid-cols-4 lg:overflow-visible lg:px-0">
            @foreach ($koleksiLain as $item)
                <div class="w-[260px] shrink-0 lg:w-auto">
                    <x-kartu-koleksi :koleksi="$item" />
                </div>
            @endforeach

            <a href="{{ route('koleksi.index', ['kategori' => $koleksi->category_id]) }}"
               class="group flex w-[260px] shrink-0 flex-col justify-center gap-4 rounded-kartu bg-lembut p-8 transition hover:bg-foto lg:w-auto">
                <p class="teks-h2">Semua koleksi {{ $koleksi->category->name }} di katalog</p>
                <span class="tautan self-start">Buka katalog {{ $koleksi->category->name }}</span>
            </a>
        </div>
    </section>
@endsection

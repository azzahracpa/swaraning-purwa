@extends('layouts.publik')

@section('title', 'Artikel Edukasi')

@section('content')
    <section class="wadah pt-6">
        <x-breadcrumb :items="['Beranda' => route('beranda'), 'Artikel Edukasi' => null]" />

        <div class="mt-8 grid gap-4 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-end lg:gap-16">
            <h1 class="teks-display-l uppercase" x-data x-muncul>Artikel <em class="aksen-miring">edukasi</em></h1>
            <p class="text-isi text-cokelat-600 lg:pb-2" x-data x-muncul="120">
                Tulisan pengelola tentang sejarah, koleksi, dan pelestarian cagar budaya. Baca sebelum atau sesudah berkunjung.
            </p>
        </div>
    </section>

    @if ($artikel->isEmpty())
        <section class="wadah py-16">
            <x-keadaan-kosong judul="Belum ada artikel terbit" ikon="daftar">
                Artikel akan tampil di sini setelah pengelola menerbitkannya.
            </x-keadaan-kosong>
        </section>
    @else
        {{-- Sorotan: artikel terbaru di halaman pertama --}}
        @if ($artikel->onFirstPage())
            @php $sorotan = $artikel->first(); @endphp
            <section class="wadah pt-10">
                <a href="{{ route('artikel.show', $sorotan) }}" class="group grid items-center gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.75fr)] lg:gap-10" x-data x-muncul>
                    <div class="h-[220px] overflow-hidden rounded-kartu bg-foto sm:h-[340px] lg:h-[460px]">
                        @if ($sorotan->gambar_url)
                            <img src="{{ $sorotan->gambar_url }}" alt="Foto sampul {{ $sorotan->title }}"
                                 class="size-full object-cover transition duration-500 group-hover:scale-[1.03]">
                        @else
                            <x-tempat-foto nada="lembut" class="size-full rounded-none" keterangan="Foto sampul artikel" />
                        @endif
                    </div>
                    <div class="flex flex-col gap-4">
                        <p class="flex flex-wrap items-center gap-2 text-kecil text-cokelat-600">
                            01 · {{ $sorotan->published_at->translatedFormat('j F Y') }} · Penulis: {{ $sorotan->author->name }}
                            <x-penanda-contoh />
                        </p>
                        <h2 class="teks-h1 transition-colors group-hover:text-maroon">{{ $sorotan->title }}</h2>
                        <p class="text-isi text-cokelat-600">{{ $sorotan->ringkasan() }}</p>
                        <span class="tautan self-start">Baca artikel</span>
                    </div>
                </a>
            </section>
        @endif

        <section class="wadah grid gap-6 pb-24 pt-16 lg:grid-cols-[304px_minmax(0,1fr)] lg:gap-10 lg:pb-32 lg:pt-24">
            <x-label-seksi nomor="01" label="Semua artikel" class="lg:pt-6" />

            <div>
                <div class="border-t border-cokelat">
                    @foreach ($artikel as $item)
                        {{-- Artikel sorotan tidak diulang di daftar halaman pertama --}}
                        @continue($artikel->onFirstPage() && $loop->first)
                        <x-baris-artikel :artikel="$item" :nomor="$artikel->firstItem() + $loop->index" x-data x-muncul="{{ $loop->index * 60 }}" />
                    @endforeach
                </div>

                @if ($artikel->onFirstPage() && $artikel->count() === 1)
                    <p class="py-6 text-kecil text-cokelat-600">Belum ada artikel lain.</p>
                @endif

                <div class="mt-8">
                    {{ $artikel->links('components.paginasi-sederhana') }}
                </div>
            </div>
        </section>
    @endif
@endsection

@extends('layouts.publik')

@section('title', $article->title)
@section('deskripsi', $article->ringkasan(150))

@section('content')
    <article>
        <header class="wadah pt-6">
            <x-breadcrumb :items="['Beranda' => route('beranda'), 'Artikel Edukasi' => route('artikel.index'), $article->title => null]" />

            <p class="mt-10 flex flex-wrap items-center gap-2 text-kecil text-cokelat-600">
                {{ $article->published_at->translatedFormat('j F Y') }} · Penulis: {{ $article->author->name }}
                <x-penanda-contoh />
            </p>
            <h1 class="teks-display-l mt-5 max-w-[960px] uppercase" x-data x-muncul>{{ $article->title }}</h1>
            <p class="teks-lead mt-5 max-w-[760px] text-cokelat-600">{{ $article->ringkasan(220) }}</p>

            <div class="mt-10 h-[220px] overflow-hidden rounded-banner bg-cokelat sm:h-[380px] lg:h-[560px]" x-data x-muncul="150">
                @if ($article->gambar_url)
                    <img src="{{ $article->gambar_url }}" alt="Foto sampul {{ $article->title }}"
                         class="size-full object-cover">
                @else
                    <x-tempat-foto class="size-full rounded-none" keterangan="Foto sampul artikel" />
                @endif
            </div>
        </header>

        <div class="wadah grid gap-8 pt-14 lg:grid-cols-[304px_minmax(0,720px)] lg:gap-10 lg:pt-20">
            <div>
                <a href="{{ route('artikel.index') }}" class="group inline-flex items-center gap-2 text-label font-semibold text-maroon lg:sticky lg:top-28">
                    <x-ikon nama="panah-kiri" class="size-4 transition-transform group-hover:-translate-x-1" />
                    Kembali ke daftar artikel
                </a>
            </div>

            {{-- Isi artikel ditampilkan sebagai teks biasa (di-escape), dipecah per paragraf --}}
            <div class="flex flex-col gap-5 text-isi text-cokelat">
                @foreach ($article->blokIsi() as $blok)
                    @if ($blok['jenis'] === 'subjudul')
                        <h2 class="teks-h2 mt-4">{{ $blok['teks'] }}</h2>
                    @else
                        <p class="max-w-[70ch]">{!! nl2br(e($blok['teks'])) !!}</p>
                    @endif
                @endforeach
            </div>
        </div>
    </article>

    @if ($artikelLain->isNotEmpty())
        <section class="wadah grid gap-6 pb-24 pt-20 lg:grid-cols-[304px_minmax(0,1fr)] lg:gap-10 lg:pb-32 lg:pt-28">
            <x-label-seksi nomor="01" label="Artikel lain" class="lg:pt-6" />
            <div class="border-t border-cokelat">
                @foreach ($artikelLain as $item)
                    <x-baris-artikel :artikel="$item" :nomor="$loop->iteration + 1" />
                @endforeach
            </div>
        </section>
    @else
        <div class="pb-24"></div>
    @endif
@endsection

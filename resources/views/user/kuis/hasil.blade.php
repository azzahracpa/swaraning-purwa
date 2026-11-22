@extends('layouts.user')

@section('title', 'Hasil '.$attempt->quiz->title)

@php
    $jumlahBenar = $ringkasan->where('benar', true)->count();
    $jumlahSoal = $ringkasan->count();
@endphp

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Kuis Edukasi' => route('kuis.index'), 'Hasil kuis' => null]" />
    <h1 class="teks-display-l mt-6 uppercase" x-data x-muncul>Hasil <span class="aksen-miring">kuis</span></h1>
@endsection

@section('content')
    {{-- Kartu skor dengan angka yang menghitung naik --}}
    <div class="relative overflow-hidden rounded-banner bg-maroon p-6 text-ivory sm:p-10"
         x-data="{ tampil: 0 }"
         x-init="const target = {{ (int) $attempt->score }}; const mulai = performance.now();
                 const jalan = (t) => { const p = Math.min(1, (t - mulai) / 900); tampil = Math.round(target * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(jalan) };
                 window.matchMedia('(prefers-reduced-motion: reduce)').matches ? tampil = target : requestAnimationFrame(jalan)">
        <div class="pointer-events-none absolute -right-24 -top-24 size-[300px] rounded-full border border-emas/70 bg-kawung bg-[length:50px_50px] opacity-30" aria-hidden="true"></div>
        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:gap-10">
            <div class="flex size-40 shrink-0 flex-col items-center justify-center rounded-full border border-emas">
                <span class="font-display text-[64px] font-medium leading-none text-emas" x-text="tampil">{{ $attempt->score }}</span>
                <span class="text-kecil text-di-gelap">dari {{ $totalPoin }} poin</span>
            </div>
            <div class="flex flex-col gap-3">
                <p class="flex items-center gap-2 text-label font-semibold"><x-belah-ketupat /> {{ $attempt->quiz->title }} <x-penanda-contoh latar="gelap" /></p>
                <p class="font-display text-h1-ponsel font-medium uppercase lg:text-h1">
                    <em class="text-emas">{{ $jumlahBenar }} dari {{ $jumlahSoal }}</em> soal benar
                </p>
                <p class="text-kecil text-di-gelap">
                    Skor tersimpan pada {{ $attempt->completed_at->translatedFormat('j M Y, H.i') }}. Skor terbaik Anda untuk kuis ini: {{ $skorTerbaik }}.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        @if ($attempt->quiz->is_active)
            <form method="POST" action="{{ route('kuis.mulai', $attempt->quiz) }}">
                @csrf
                <button type="submit" class="tombol tombol-utama">Kerjakan lagi</button>
            </form>
        @endif
        <a href="{{ route('kuis.index') }}" class="tombol tombol-garis">Kembali ke daftar kuis</a>
    </div>

    <section class="mt-12 flex flex-col gap-5" x-data="{ semua: false }">
        <x-label-seksi nomor="01" label="Ringkasan jawaban" />
        <ol class="border-t border-cokelat">
            @foreach ($ringkasan as $i => $baris)
                <li class="flex items-center gap-4 border-b border-garis py-4" @if ($i >= 5) x-show="semua" x-cloak @endif>
                    <span class="font-display text-angka-sedang text-emas-700">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-label font-semibold">{{ $baris['teks'] }}</p>
                        <p class="text-kecil text-cokelat-600">
                            Jawaban Anda: {{ $baris['dipilih'] ?? 'tidak dijawab' }}
                            @unless ($baris['benar'])
                                · Jawaban benar: {{ $baris['kunci'] }}
                            @endunless
                        </p>
                    </div>
                    @if ($baris['benar'])
                        <x-badge-keadaan nada="positif" class="gap-1.5"><x-ikon nama="centang" class="size-3.5" />Benar</x-badge-keadaan>
                    @else
                        <x-badge-keadaan nada="negatif" class="gap-1.5"><x-ikon nama="tutup" class="size-3.5" />Salah</x-badge-keadaan>
                    @endif
                </li>
            @endforeach
        </ol>
        @if ($ringkasan->count() > 5)
            <button type="button" class="tautan self-start" x-on:click="semua = ! semua"
                    x-text="semua ? 'Tampilkan lebih sedikit' : 'Tampilkan semua {{ $ringkasan->count() }} soal'"></button>
        @endif
    </section>
@endsection

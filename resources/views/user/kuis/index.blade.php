@extends('layouts.user')

@section('title', 'Kuis Edukasi')

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => route('dashboard'), 'Kuis Edukasi' => null]" />
    <h1 class="teks-display-l mt-6 uppercase" x-data x-muncul>Kuis <span class="aksen-miring">edukasi</span></h1>
    <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
        Pilih kuis yang aktif. Skor dihitung otomatis dan tersimpan setiap kali Anda menyelesaikan kuis.
    </p>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1fr)_280px]">
        <section class="flex flex-col gap-5">
            <x-label-seksi nomor="01" label="Kuis aktif" />

            @if ($kuis->isEmpty())
                <x-keadaan-kosong judul="Belum ada kuis aktif" ikon="bantuan">
                    Pengelola belum mengaktifkan kuis. Sambil menunggu, baca artikel edukasi di situs ini.
                    <x-slot:aksi><a href="{{ route('artikel.index') }}" class="tombol tombol-garis tombol-kecil">Baca artikel</a></x-slot:aksi>
                </x-keadaan-kosong>
            @else
                <div class="border-t border-cokelat">
                    @foreach ($kuis as $item)
                        @php $terbaik = $skorTerbaik[$item->id] ?? null; @endphp
                        <article class="flex flex-col gap-4 border-b border-garis py-6 sm:flex-row sm:items-center sm:gap-6" x-data x-muncul="{{ $loop->index * 80 }}">
                            <span class="font-display text-angka text-emas-700">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="flex min-w-0 flex-1 flex-col gap-2">
                                <h2 class="flex flex-wrap items-center gap-3 teks-h3">{{ $item->title }}</h2>
                                @if ($item->description)
                                    <p class="text-kecil text-cokelat-600">{{ $item->description }}</p>
                                @endif
                                <div class="flex flex-wrap gap-2">
                                    <x-label-kategori>{{ $item->questions_count }} soal</x-label-kategori>
                                    <x-label-kategori>{{ $terbaik !== null ? 'Skor terbaik '.$terbaik : 'Belum dikerjakan' }}</x-label-kategori>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('kuis.mulai', $item) }}">
                                @csrf
                                <button type="submit" class="tombol {{ $terbaik !== null ? 'tombol-garis' : ($loop->first || $skorTerbaik->isEmpty() ? 'tombol-utama' : 'tombol-garis') }}">
                                    {{ $terbaik !== null ? 'Kerjakan lagi' : 'Kerjakan' }}
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
                <p class="text-kecil text-cokelat-600">Kuis yang dinonaktifkan pengelola tidak ditampilkan di sini.</p>
            @endif
        </section>

        <aside class="flex flex-col gap-4 self-start rounded-kartu bg-lembut p-5">
            <div class="flex items-center justify-between">
                <h2 class="teks-h3">Riwayat pengerjaan</h2>
            </div>
            @forelse ($riwayat as $item)
                <a href="{{ route('kuis.hasil', $item) }}" class="group flex items-center justify-between gap-3 border-t border-garis-kuat pt-3">
                    <span>
                        <span class="block text-label font-semibold group-hover:text-maroon">{{ $item->quiz->title }}</span>
                        <span class="text-kecil text-cokelat-600">{{ $item->completed_at->translatedFormat('j M Y') }}</span>
                    </span>
                    <span class="whitespace-nowrap"><span class="font-display text-h3 text-maroon">{{ $item->score }}</span></span>
                </a>
            @empty
                <p class="text-kecil text-cokelat-600">Anda belum mengerjakan kuis. Skor terakhir akan tampil di sini.</p>
            @endforelse
            @if ($riwayat->isNotEmpty())
                <a href="{{ route('kuis.hasil', $riwayat->first()) }}" class="tautan self-start">Lihat hasil terakhir</a>
            @endif
        </aside>
    </div>
@endsection

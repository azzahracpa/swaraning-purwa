@extends('layouts.user')

@section('title', 'Akun saya')

@php
    use App\Support\Navigasi;

    $totalReservasi = $jumlahStatus->sum();
    $statusRingkas = [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => null]" />
    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="teks-display-l uppercase" x-data x-muncul>
                Halo, <span class="aksen-miring">{{ $user->name }}</span>
            </h1>
            <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
                {{ now()->translatedFormat('l, j F Y') }}. Ringkasan reservasi, koleksi favorit, dan kuis Anda ada di sini.
            </p>
        </div>
        <a href="{{ Navigasi::url('reservasi.index') }}" class="tombol tombol-utama hidden self-start lg:inline-flex lg:self-auto">Ajukan reservasi baru</a>
    </div>
@endsection

@section('content')
    {{-- 01 Kunjungan berikutnya --}}
    <section class="flex flex-col gap-6" x-data x-muncul>
        <x-label-seksi nomor="01" label="Kunjungan berikutnya" />

        @if ($kunjungan)
            @php
                $slot = $kunjungan->schedule;
                $sisaHari = (int) today()->diffInDays($slot->visit_date, false);
            @endphp
            <div class="kartu grid overflow-hidden md:grid-cols-[220px_minmax(0,1fr)]">
                <div class="flex flex-col items-center justify-center gap-1 bg-maroon p-6 text-center text-ivory">
                    <p class="teks-label-kecil uppercase tracking-[0.1em] text-emas">{{ $slot->visit_date->translatedFormat('l') }}</p>
                    <p class="font-display text-display-l font-medium leading-none">{{ $slot->visit_date->format('j') }}</p>
                    <p class="text-label">{{ $slot->visit_date->translatedFormat('F Y') }}</p>
                    <p class="mt-2 rounded-full bg-ivory/10 px-3 py-1 text-kecil">
                        {{ $sisaHari === 0 ? 'Hari ini' : ($sisaHari === 1 ? 'Besok' : $sisaHari.' hari lagi') }}
                    </p>
                </div>
                <div class="flex flex-col gap-4 p-6 sm:p-8">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-badge-status :status="$kunjungan->status" />
                        <span class="text-kecil text-cokelat-600">Kode booking <strong class="text-maroon">{{ $kunjungan->booking_code }}</strong></span>
                    </div>
                    <h2 class="teks-h2">Kunjungan pukul {{ $slot->rentangJam() }}</h2>
                    <p class="max-w-[520px] text-isi text-cokelat-600">
                        @if ($kunjungan->status === 'approved')
                            Reservasi untuk {{ $kunjungan->total_visitors }} orang sudah disetujui. Tunjukkan kode booking di meja penerima tamu.
                        @else
                            Pengajuan untuk {{ $kunjungan->total_visitors }} orang sedang ditinjau pengelola. Kuota baru terpakai setelah disetujui.
                        @endif
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('reservasi.show', $kunjungan) }}" class="tombol tombol-utama max-sm:w-full">Lihat detail reservasi</a>
                        <a href="{{ Navigasi::url('reservasi.riwayat') }}" class="tombol tombol-garis max-sm:w-full">Semua riwayat</a>
                    </div>
                </div>
            </div>
        @else
            <div class="kartu flex flex-col gap-4 p-6 sm:p-8">
                <h2 class="teks-h2">Belum ada kunjungan terjadwal</h2>
                <p class="max-w-[520px] text-isi text-cokelat-600">
                    Pilih jadwal yang tersedia, isi formulir, lalu tunggu keputusan pengelola. Statusnya muncul di sini.
                </p>
                <a href="{{ Navigasi::url('reservasi.index') }}" class="tombol tombol-utama self-start max-sm:w-full">Pilih jadwal kunjungan</a>
            </div>
        @endif
    </section>

    {{-- 02 Ringkasan --}}
    <section class="mt-14 flex flex-col gap-6" x-data x-muncul="100">
        <x-label-seksi nomor="02" label="Ringkasan" />
        <div class="grid border-t border-cokelat md:grid-cols-3">
            {{-- Reservasi --}}
            <div class="flex flex-col gap-3 border-b border-garis py-6 md:border-b-0 md:border-r md:pr-6">
                <div class="flex items-baseline justify-between gap-2">
                    <h3 class="teks-h3">Reservasi</h3>
                    <span class="font-display text-angka font-medium text-maroon">{{ $totalReservasi }}</span>
                </div>
                @if ($totalReservasi)
                    <ul class="flex flex-col gap-2 text-kecil">
                        @foreach ($statusRingkas as $kunci => $label)
                            @php $n = (int) ($jumlahStatus[$kunci] ?? 0); @endphp
                            <li class="flex items-center gap-3">
                                <span class="w-20 text-cokelat-600">{{ $label }}</span>
                                <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-garis">
                                    <span class="block h-full bg-maroon transition-all duration-700" style="width: {{ $n / $totalReservasi * 100 }}%"></span>
                                </span>
                                <span class="w-5 text-right font-semibold">{{ $n }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-kecil text-cokelat-600">Belum ada pengajuan reservasi. Riwayat dan statusnya akan tampil di sini.</p>
                @endif
                <a href="{{ Navigasi::url('reservasi.riwayat') }}" class="tautan mt-auto self-start">Buka riwayat</a>
            </div>

            {{-- Favorit --}}
            <div class="flex flex-col gap-3 border-b border-garis py-6 md:border-b-0 md:border-r md:px-6">
                <div class="flex items-baseline justify-between gap-2">
                    <h3 class="teks-h3">Koleksi favorit</h3>
                    <span class="font-display text-angka font-medium text-maroon">{{ $jumlahFavorit }}</span>
                </div>
                @if ($favorit->isNotEmpty())
                    <div class="flex -space-x-3">
                        @foreach ($favorit as $item)
                            <a href="{{ route('koleksi.show', $item) }}" title="{{ $item->name }}"
                               class="size-14 shrink-0 overflow-hidden rounded-full border-2 border-putih bg-foto transition hover:z-10 hover:-translate-y-1">
                                @if ($item->primaryImage)
                                    <img src="{{ $item->primaryImage->url }}" alt="{{ $item->name }}" class="size-full object-cover" loading="lazy">
                                @else
                                    <span class="flex size-full items-center justify-center text-emas-700"><x-ikon nama="gambar" class="size-5" /></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                    <p class="text-kecil text-cokelat-600">Terakhir disimpan: {{ $favorit->first()->name }}</p>
                @else
                    <p class="text-kecil text-cokelat-600">Belum ada koleksi tersimpan. Tekan “Simpan ke favorit” di halaman detail koleksi.</p>
                @endif
                <a href="{{ Navigasi::url($jumlahFavorit ? 'favorit.index' : 'koleksi.index') }}" class="tautan mt-auto self-start">
                    {{ $jumlahFavorit ? 'Lihat semua favorit' : 'Buka katalog koleksi' }}
                </a>
            </div>

            {{-- Kuis --}}
            <div class="flex flex-col gap-3 py-6 md:pl-6">
                <h3 class="teks-h3">Kuis</h3>
                @if ($kuisTerakhir)
                    @php $maks = max(1, $kuisTerakhir->quiz->totalPoin()); @endphp
                    <div class="flex items-center gap-4">
                        <div class="relative size-16 shrink-0">
                            <svg viewBox="0 0 36 36" class="size-full -rotate-90" aria-hidden="true">
                                <circle cx="18" cy="18" r="15.5" fill="none" stroke-width="3" class="stroke-garis" />
                                <circle cx="18" cy="18" r="15.5" fill="none" stroke-width="3" stroke-linecap="round" class="stroke-maroon"
                                        stroke-dasharray="{{ round($kuisTerakhir->score / $maks * 97.4, 1) }} 97.4" />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-label font-semibold">{{ $kuisTerakhir->score }}</span>
                        </div>
                        <div class="text-kecil">
                            <p class="font-semibold">{{ $kuisTerakhir->quiz->title }}</p>
                            <p class="text-cokelat-600">Skor {{ $kuisTerakhir->score }} dari {{ $maks }} poin · {{ $kuisTerakhir->completed_at->translatedFormat('j M') }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-kecil text-cokelat-600">Anda belum mengerjakan kuis. Skor terakhir akan tampil di sini.</p>
                @endif
                @if ($kuisBelumDikerjakan)
                    <p class="text-kecil"><span class="font-semibold text-maroon">{{ $kuisBelumDikerjakan }} kuis</span> belum Anda kerjakan.</p>
                @endif
                <a href="{{ Navigasi::url('kuis.index') }}" class="tautan mt-auto self-start">Lihat daftar kuis</a>
            </div>
        </div>
    </section>

    {{-- 03 Cara reservasi diproses --}}
    <section class="mt-14 flex flex-col gap-6" x-data x-muncul="200">
        <x-label-seksi nomor="03" label="Cara reservasi diproses" />
        <ol class="grid gap-4 md:grid-cols-3">
            @foreach ([
                ['Ajukan', 'Pilih slot jadwal yang masih tersedia lalu isi jumlah orang dan nomor telepon.'],
                ['Ditinjau', 'Pengelola memeriksa sisa kursi. Status Menunggu belum mengurangi kuota.'],
                ['Datang', 'Setelah Disetujui, tunjukkan kode booking saat tiba di museum.'],
            ] as $i => [$judul, $isi])
                <li class="kartu flex flex-col gap-2 p-6 transition hover:-translate-y-1 hover:border-emas">
                    <span class="font-display text-angka font-medium text-emas-700">0{{ $i + 1 }}</span>
                    <p class="teks-h3">{{ $judul }}</p>
                    <p class="text-kecil text-cokelat-600">{{ $isi }}</p>
                </li>
            @endforeach
        </ol>
    </section>
@endsection

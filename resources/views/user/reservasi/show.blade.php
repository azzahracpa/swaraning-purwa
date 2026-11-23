@extends('layouts.user')

@section('title', $reservasi->booking_code)

@php $terkirim = session('terkirim'); @endphp

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => route('dashboard'), 'Riwayat Reservasi' => route('reservasi.riwayat'), $reservasi->booking_code => null]" />
    @if ($terkirim)
        <h1 class="teks-display-l mt-6 uppercase" x-data x-muncul>Pengajuan <span class="aksen-miring">terkirim</span></h1>
        <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
            Reservasi Anda tersimpan dengan status Menunggu. Pengelola akan memeriksa kapasitas slot lalu memberi keputusan.
        </p>
    @else
        <h1 class="teks-display-l mt-6 uppercase">Detail <span class="aksen-miring">reservasi</span></h1>
    @endif
@endsection

@section('content')
    <div class="kartu flex flex-col gap-5 p-6 md:flex-row md:items-center md:gap-8" x-data x-miring="2">
        <div>
            <p class="text-kecil text-cokelat-600">Kode booking</p>
            <p class="font-display text-h2-ponsel font-semibold lg:text-h2">{{ $reservasi->booking_code }}</p>
        </div>
        <x-badge-status :status="$reservasi->status" class="self-start md:self-center" />
        <div class="text-kecil">
            <p class="text-cokelat-600">Jadwal</p>
            <p class="font-semibold">{{ $reservasi->schedule->visit_date->translatedFormat('l, j M Y') }} · {{ $reservasi->schedule->rentangJam() }}</p>
        </div>
        <div class="text-kecil">
            <p class="text-cokelat-600">Jumlah</p>
            <p class="font-semibold">{{ $reservasi->total_visitors }} orang</p>
        </div>
    </div>

    <dl class="mt-6 grid gap-4 text-kecil sm:grid-cols-3">
        <div><dt class="text-cokelat-600">Penanggung jawab</dt><dd class="font-semibold">{{ $reservasi->visitor_name }}</dd></div>
        <div><dt class="text-cokelat-600">Telepon</dt><dd class="font-semibold">{{ $reservasi->phone }}</dd></div>
        <div><dt class="text-cokelat-600">Diajukan</dt><dd class="font-semibold">{{ $reservasi->created_at->translatedFormat('j M Y, H.i') }}</dd></div>
        @if ($reservasi->notes)
            <div class="sm:col-span-3"><dt class="text-cokelat-600">Catatan</dt><dd class="font-semibold">{{ $reservasi->notes }}</dd></div>
        @endif
    </dl>

    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('reservasi.riwayat') }}" class="tombol tombol-utama">Lihat riwayat reservasi</a>
        <a href="{{ route('dashboard') }}" class="tombol tombol-garis">Kembali ke dashboard</a>
        @if ($reservasi->status === 'pending')
            @include('user.reservasi._form-batal', ['item' => $reservasi])
        @endif
    </div>

    <section class="mt-14 flex flex-col gap-6">
        <x-label-seksi nomor="01" label="Apa yang terjadi selanjutnya" />
        <ol class="grid gap-6 md:grid-cols-3">
            @foreach ([
                ['Pengelola meninjau pengajuan dan kapasitas slot.', 'pending'],
                ['Status berubah menjadi Disetujui atau Ditolak.', 'approved'],
                ['Datang sesuai jadwal dan tunjukkan kode booking bila diminta.', null],
            ] as $i => [$teks, $status])
                <li class="flex flex-col gap-3 border-t border-emas pt-5" x-data x-muncul="{{ $i * 100 }}">
                    <span class="font-display text-angka text-emas-700">0{{ $i + 1 }}</span>
                    <p class="text-isi">{{ $teks }}</p>
                    @if ($status)
                        <x-badge-status :status="$status" class="self-start" />
                    @endif
                </li>
            @endforeach
        </ol>
    </section>
@endsection

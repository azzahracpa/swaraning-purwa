@extends('layouts.user')

@section('title', 'Riwayat Reservasi')

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => route('dashboard'), 'Riwayat Reservasi' => null]" />
    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="teks-display-l uppercase" x-data x-muncul>Riwayat <span class="aksen-miring">reservasi</span></h1>
            <p class="mt-4 text-isi text-cokelat-600">Semua pengajuan kunjungan Anda beserta keputusan pengelola.</p>
        </div>
        <a href="{{ route('reservasi.index') }}" class="tombol tombol-utama self-start lg:self-auto">Ajukan reservasi baru</a>
    </div>
@endsection

@section('content')
    <form method="GET" action="{{ route('reservasi.riwayat') }}" class="flex flex-col gap-3 pb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-wrap gap-2">
            <x-admin.chip :href="route('reservasi.riwayat', array_filter(['urut' => request('urut')]))" :aktif="! $status" :jumlah="$jumlah->sum()">Semua</x-admin.chip>
            @foreach (\App\Models\Reservation::LABEL_STATUS as $kunci => $label)
                <x-admin.chip :href="route('reservasi.riwayat', array_filter(['status' => $kunci, 'urut' => request('urut')]))"
                              :aktif="$status === $kunci" :jumlah="$jumlah[$kunci] ?? 0">{{ $label }}</x-admin.chip>
            @endforeach
        </div>
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <label class="relative">
            <span class="sr-only">Urutkan</span>
            <x-ikon nama="urut" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
            <select name="urut" class="kolom-input h-10 pl-9 text-kecil" x-data x-on:change="$el.form.requestSubmit()">
                <option value="">Urutkan: jadwal terdekat</option>
                <option value="terbaru" @selected(request('urut') === 'terbaru')>Urutkan: terakhir diajukan</option>
            </select>
        </label>
    </form>

    @if ($reservasi->isEmpty())
        <x-keadaan-kosong judul="{{ $status ? 'Tidak ada reservasi dengan status ini' : 'Belum ada reservasi' }}" ikon="kalender">
            Pengajuan kunjungan Anda akan tampil di sini beserta statusnya.
            <x-slot:aksi><a href="{{ route('reservasi.index') }}" class="tombol tombol-utama tombol-kecil">Ajukan reservasi</a></x-slot:aksi>
        </x-keadaan-kosong>
    @else
        <div class="kartu overflow-hidden" x-data="{ buka: null }">
            <div class="hidden grid-cols-[1.3fr_1.4fr_0.8fr_1fr_1fr_auto] gap-4 bg-lembut px-5 py-3 text-label font-semibold text-cokelat-600 md:grid">
                <span>Kode booking</span><span>Jadwal kunjungan</span><span>Jumlah</span><span>Diajukan</span><span>Status</span><span class="w-20 text-right">Aksi</span>
            </div>
            @foreach ($reservasi as $item)
                <div class="border-t border-garis first:border-t-0 md:first:border-t">
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 px-5 py-4 text-kecil md:grid-cols-[1.3fr_1.4fr_0.8fr_1fr_1fr_auto] md:items-center">
                        <span class="font-semibold text-maroon">{{ $item->booking_code }}</span>
                        <span class="md:order-none">
                            <span class="block font-semibold">{{ $item->schedule->visit_date->translatedFormat('l, j M Y') }}</span>
                            <span class="text-cokelat-600">{{ $item->schedule->rentangJam() }}</span>
                        </span>
                        <span>{{ $item->total_visitors }} orang</span>
                        <span class="text-cokelat-600">{{ $item->created_at->translatedFormat('j M Y') }}</span>
                        <x-badge-status :status="$item->status" class="justify-self-start" />
                        <button type="button" class="tombol tombol-garis tombol-kecil w-20 justify-self-end"
                                x-on:click="buka = buka === {{ $item->id }} ? null : {{ $item->id }}"
                                :aria-expanded="buka === {{ $item->id }}" x-text="buka === {{ $item->id }} ? 'Tutup' : 'Detail'">Detail</button>
                    </div>
                    <div x-show="buka === {{ $item->id }}" x-cloak x-transition.opacity
                         class="flex flex-col gap-4 bg-maroon-lembut px-5 py-4 text-kecil sm:flex-row sm:items-center">
                        <div class="grid flex-1 gap-3 sm:grid-cols-3">
                            <div><p class="text-cokelat-600">Penanggung jawab</p><p class="font-semibold">{{ $item->visitor_name }}</p></div>
                            <div><p class="text-cokelat-600">Telepon</p><p class="font-semibold">{{ $item->phone }}</p></div>
                            <div><p class="text-cokelat-600">Catatan</p><p class="font-semibold">{{ $item->notes ?: '—' }}</p></div>
                        </div>
                        <div class="flex flex-col items-start gap-1 sm:items-end">
                            @if ($item->status === 'pending')
                                @include('user.reservasi._form-batal', ['item' => $item, 'kecil' => true])
                                <p class="text-cokelat-600">Hanya selama status Menunggu.</p>
                            @else
                                <a href="{{ route('reservasi.show', $item) }}" class="tautan">Buka halaman detail</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center gap-3">
            <p class="text-kecil text-cokelat-600">Menampilkan {{ $reservasi->count() }} reservasi</p>
            <x-penanda-contoh teks="data contoh" />
        </div>
        <div class="mt-4">{{ $reservasi->links() }}</div>
    @endif
@endsection

@extends('layouts.admin')

@section('title', 'Dashboard Pengelola')
@section('halaman', 'Dashboard')

@php
    use App\Support\Navigasi;

    $puncak = max(1, $grafik->max(fn ($h) => $h['disetujui'] + $h['menunggu']));
    $hariTerpadat = $grafik->sortByDesc(fn ($h) => $h['disetujui'] + $h['menunggu'])->first();
    $kartu = [
        ['01', 'Reservasi menunggu', $statistik['menunggu'],
            $statistik['terlama'] ? 'Paling lama diajukan '.$statistik['terlama']->diffForHumans() : 'Tidak ada pengajuan tertunda', true],
        ['02', 'Koleksi tercatat', $statistik['koleksi'],
            $statistik['koleksi_bulan_ini'].' ditambahkan bulan ini, '.$statistik['koleksi_tanpa_foto'].' belum berfoto', false],
        ['03', 'Pengguna terdaftar', $statistik['pengguna'], 'Akun dengan peran user', false],
        ['04', 'Pengunjung disetujui', $statistik['pengunjung_disetujui'],
            'Untuk '.$awalPekan->translatedFormat('j').'–'.$akhirPekan->translatedFormat('j M').', dari '.$statistik['reservasi_disetujui'].' reservasi', false],
    ];
@endphp

@section('content')
    <x-admin.kepala judul="Dashboard" aksen="pengelola"
                    :deskripsi="now()->translatedFormat('l, j F Y').'. Ada '.$statistik['menunggu'].' reservasi menunggu keputusan'.($statistik['menunggu_pekan_ini'] ? ', '.$statistik['menunggu_pekan_ini'].' di antaranya untuk kunjungan pekan ini.' : '.')">
        <a href="{{ Navigasi::url('admin.koleksi.create') }}" class="tombol tombol-garis">Tambah koleksi</a>
        <a href="{{ Navigasi::url('admin.reservasi.index') }}" class="tombol tombol-utama">Tinjau reservasi</a>
    </x-admin.kepala>

    {{-- 01 Ringkasan --}}
    <section class="flex flex-col gap-4">
        <div class="flex items-center gap-3"><x-label-seksi nomor="01" label="Ringkasan hari ini" /> <x-penanda-contoh teks="data contoh" /></div>
        <div class="grid overflow-hidden rounded-kartu border border-garis bg-putih sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($kartu as $i => [$nomor, $judul, $angka, $ket, $sorot])
                <div class="flex flex-col gap-2 p-6 transition {{ $sorot ? 'bg-maroon text-ivory' : 'border-garis hover:bg-ivory/60 sm:border-l' }}"
                     x-data="{ n: 0 }"
                     x-init="const t = {{ (int) $angka }}, m = performance.now();
                             const j = (w) => { const p = Math.min(1, (w - m) / 800); n = Math.round(t * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(j) };
                             matchMedia('(prefers-reduced-motion: reduce)').matches ? n = t : requestAnimationFrame(j)">
                    <p class="teks-label-kecil uppercase tracking-[0.1em] {{ $sorot ? 'text-emas' : 'text-cokelat-600' }}">
                        <span class="{{ $sorot ? '' : 'text-emas-700' }}">{{ $nomor }}</span> {{ $judul }}
                    </p>
                    <p class="font-display text-angka font-medium" x-text="n">{{ $angka }}</p>
                    <p class="text-kecil {{ $sorot ? 'text-di-gelap' : 'text-cokelat-600' }}">{{ $ket }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 02 Reservasi terbaru --}}
    <section class="mt-12 flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <x-label-seksi nomor="02" label="Reservasi terbaru" />
            <a href="{{ Navigasi::url('admin.reservasi.index') }}" class="tautan">Lihat semua reservasi</a>
        </div>

        @if ($terbaru->isEmpty())
            <x-keadaan-kosong judul="Belum ada reservasi" ikon="daftar" />
        @else
            <div class="kartu overflow-x-auto">
                <table class="tabel min-w-[860px]">
                    <thead class="bg-lembut">
                        <tr><th>Kode booking</th><th>Pengunjung</th><th>Jadwal</th><th>Sisa kapasitas slot</th><th>Status</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($terbaru as $item)
                            @php
                                $slot = $item->schedule;
                                $sisa = max(0, $slot->capacity - (int) $slot->jumlah_disetujui);
                                $cukup = $item->total_visitors <= $sisa;
                                $bisaAksi = $item->status === 'pending' && Route::has('admin.reservasi.setujui');
                            @endphp
                            <tr>
                                <td>
                                    <p class="font-semibold text-maroon">{{ $item->booking_code }}</p>
                                    <p class="text-cokelat-600">Diajukan {{ $item->created_at->translatedFormat('j M') }}</p>
                                </td>
                                <td><p class="font-semibold">{{ $item->visitor_name }}</p><p class="text-cokelat-600">{{ $item->total_visitors }} orang</p></td>
                                <td class="whitespace-nowrap">{{ $slot->visit_date->translatedFormat('D, j M Y') }}<br><span class="text-cokelat-600">{{ $slot->rentangJam() }}</span></td>
                                <td>
                                    <div class="h-1.5 w-28 overflow-hidden rounded-full bg-garis">
                                        <div class="h-full bg-maroon" style="width: {{ min(100, (int) $slot->jumlah_disetujui / max(1, $slot->capacity) * 100) }}%"></div>
                                    </div>
                                    <p class="mt-1 {{ $item->status === 'pending' && ! $cukup ? 'text-status-ditolak-teks' : 'text-cokelat-600' }}">Sisa {{ $sisa }} dari {{ $slot->capacity }} kursi</p>
                                </td>
                                <td><x-badge-status :status="$item->status" /></td>
                                <td>
                                    <div class="flex flex-col items-end gap-1">
                                        @if ($bisaAksi)
                                            @php $ringkas = $item->visitor_name.' · '.$item->total_visitors.' orang · '.$slot->visit_date->translatedFormat('D, j M Y').' · '.$slot->rentangJam(); @endphp
                                            <div class="flex gap-2">
                                                <form method="POST" action="{{ route('admin.reservasi.tolak', $item) }}" x-data
                                                      x-on:submit.prevent="$store.konfirmasi.buka($el, { judul: @js('Tolak '.$item->booking_code.'?'), isi: 'Status berubah menjadi Ditolak dan tidak bisa dikembalikan ke Menunggu.', ringkasan: @js($ringkas), tombol: 'Ya, tolak', jenis: 'tolak' })">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="tombol tombol-garis tombol-kecil">Tolak</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reservasi.setujui', $item) }}" x-data
                                                      x-on:submit.prevent="$store.konfirmasi.buka($el, { judul: @js('Setujui '.$item->booking_code.'?'), isi: 'Sistem memeriksa ulang sisa kursi saat Anda menekan tombol ini.', ringkasan: @js($ringkas), tombol: 'Ya, setujui', jenis: 'setujui' })">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="tombol tombol-utama tombol-kecil" @disabled(! $cukup)>Setujui</button>
                                                </form>
                                            </div>
                                            @unless ($cukup)
                                                <p class="max-w-[200px] text-right text-kecil text-status-ditolak-teks">Sisa {{ $sisa }} kursi, pengajuan {{ $item->total_visitors }} orang. Tidak bisa disetujui.</p>
                                            @endunless
                                        @else
                                            <a href="{{ Route::has('admin.reservasi.index') ? route('admin.reservasi.index', ['status' => 'semua', 'pilih' => $item->id]) : '#' }}" class="tombol tombol-garis tombol-kecil">Detail</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <p class="flex items-center gap-2 text-kecil text-cokelat-600">
            <x-ikon nama="info" class="size-4 shrink-0" /> Kuota hanya berkurang setelah disetujui. Sistem memeriksa ulang sisa kursi saat Anda menekan Setujui.
        </p>
    </section>

    <div class="mt-12 grid gap-8 xl:grid-cols-[minmax(0,1fr)_340px]">
        {{-- 03 Grafik pengunjung 7 hari --}}
        <section class="flex flex-col gap-4">
            <div class="flex items-center gap-3"><x-label-seksi nomor="03" label="Pengunjung 7 hari ke depan" /> <x-penanda-contoh teks="data contoh" /></div>
            <div class="kartu flex flex-col gap-4 p-6">
                <div class="flex flex-wrap gap-5 text-kecil text-cokelat-600">
                    <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-maroon"></span> Disetujui</span>
                    <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-emas"></span> Menunggu (belum mengurangi kuota)</span>
                </div>
                <div class="grid h-[220px] grid-cols-7 items-end gap-3" x-data="{ sorot: null }" role="img"
                     aria-label="Grafik jumlah pengunjung 7 hari ke depan">
                    @foreach ($grafik as $i => $hari)
                        @php $total = $hari['disetujui'] + $hari['menunggu']; @endphp
                        <div class="relative flex h-full flex-col items-center justify-end gap-1"
                             x-on:mouseenter="sorot = {{ $i }}" x-on:mouseleave="sorot = null">
                            <span class="text-label font-semibold">{{ $total }}</span>
                            <div class="flex w-full max-w-[44px] flex-col justify-end overflow-hidden rounded-t-[4px] transition-transform duration-200 origin-bottom"
                                 :class="sorot === {{ $i }} && 'scale-x-110'"
                                 style="height: {{ max(2, $total / $puncak * 150) }}px">
                                <div class="bg-emas" style="height: {{ $total ? $hari['menunggu'] / $total * 100 : 0 }}%"></div>
                                <div class="flex-1 bg-maroon"></div>
                            </div>
                            <span class="text-kecil text-cokelat-600">{{ $hari['tanggal']->translatedFormat('D') }}</span>
                            <span class="text-kecil font-semibold">{{ $hari['tanggal']->format('j') }}</span>
                            <div x-show="sorot === {{ $i }}" x-cloak x-transition.opacity
                                 class="absolute bottom-full z-10 mb-2 w-40 rounded-input bg-cokelat px-3 py-2 text-kecil text-ivory shadow-dialog">
                                <p class="font-semibold">{{ $hari['tanggal']->translatedFormat('l, j M') }}</p>
                                <p>Disetujui: {{ $hari['disetujui'] }} orang</p>
                                <p>Menunggu: {{ $hari['menunggu'] }} orang</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-kecil text-cokelat-600">
                    Angka di atas batang = jumlah orang.
                    @if ($hariTerpadat && $hariTerpadat['disetujui'] + $hariTerpadat['menunggu'] > 0)
                        {{ $hariTerpadat['tanggal']->translatedFormat('l, j M') }} paling padat.
                    @endif
                </p>
            </div>
        </section>

        {{-- 04 Akses cepat --}}
        <section class="flex flex-col gap-4">
            <x-label-seksi nomor="04" label="Akses cepat" />
            <div class="kartu divide-y divide-garis">
                @foreach ([
                    ['tambah', 'Tambah koleksi', 'Data, kategori, dan foto', 'admin.koleksi.create'],
                    ['edit', 'Tulis artikel', 'Draf atau langsung terbit', 'admin.artikel.create'],
                    ['kalender', 'Buka slot jadwal', 'Tanggal, jam, dan kapasitas', 'admin.jadwal.index'],
                    ['bantuan', 'Buat kuis', 'Soal dan pilihan jawaban', 'admin.kuis.create'],
                ] as [$ikon, $judul, $ket, $rute])
                    <a href="{{ Navigasi::url($rute) }}" class="group flex items-center gap-4 p-4 transition hover:bg-ivory/60">
                        <span class="flex size-10 items-center justify-center rounded-input bg-lembut text-maroon"><x-ikon :nama="$ikon" /></span>
                        <span class="flex-1">
                            <span class="block text-label font-semibold">{{ $judul }}</span>
                            <span class="text-kecil text-cokelat-600">{{ $ket }}</span>
                        </span>
                        <x-ikon nama="chevron-kanan" class="text-cokelat-600 transition-transform group-hover:translate-x-1" />
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection

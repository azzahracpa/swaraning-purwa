@extends('layouts.admin')

@section('title', 'Kelola Reservasi')
@section('halaman', 'Kelola Reservasi')

@php
    $urlDengan = fn (array $ubah) => route('admin.reservasi.index', array_filter(array_merge(
        ['status' => $status, 'tanggal' => $tanggal, 'urut' => request('urut'), 'cari' => $cari ?: null], $ubah
    ), fn ($v) => $v !== null && $v !== ''));
@endphp

@section('content')
    <x-admin.kepala judul="Kelola" aksen="reservasi"
                    deskripsi="Tinjau pengajuan berstatus Menunggu. Persetujuan hanya bisa diberikan bila sisa kursi pada slot mencukupi.">
        @if (Route::has('admin.jadwal.index'))
            <a href="{{ route('admin.jadwal.index') }}" class="tombol tombol-garis">Kelola jadwal</a>
        @endif
    </x-admin.kepala>

    <form method="GET" action="{{ route('admin.reservasi.index') }}" class="flex flex-col gap-3 pb-6 lg:flex-row lg:items-center">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="flex flex-wrap gap-2">
            @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan', 'semua' => 'Semua'] as $kunci => $label)
                <x-admin.chip :href="$urlDengan(['status' => $kunci, 'pilih' => null])" :aktif="$status === $kunci"
                              :jumlah="$kunci === 'semua' ? $jumlah->sum() : ($jumlah[$kunci] ?? 0)">{{ $label }}</x-admin.chip>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-2 lg:ml-auto">
            <label class="relative">
                <span class="sr-only">Tanggal kunjungan</span>
                <x-ikon nama="kalender" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
                <select name="tanggal" class="kolom-input h-10 pl-9 text-kecil" x-data x-on:change="$el.form.requestSubmit()">
                    <option value="">Semua tanggal</option>
                    @foreach ($daftarTanggal as $t)
                        <option value="{{ $t }}" @selected($tanggal === $t)>{{ \Illuminate\Support\Carbon::parse($t)->translatedFormat('D, j M Y') }}</option>
                    @endforeach
                </select>
            </label>
            <label class="relative">
                <span class="sr-only">Urutkan</span>
                <x-ikon nama="urut" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
                <select name="urut" class="kolom-input h-10 pl-9 text-kecil" x-data x-on:change="$el.form.requestSubmit()">
                    <option value="">Terlama diajukan dulu</option>
                    <option value="jadwal" @selected(request('urut') === 'jadwal')>Jadwal terdekat</option>
                </select>
            </label>
        </div>
    </form>

    @if ($cari)
        <p class="mb-4 text-kecil text-cokelat-600">Hasil pencarian "{{ $cari }}" · <a href="{{ route('admin.reservasi.index') }}" class="tautan">hapus pencarian</a></p>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
        <div class="min-w-0">
            @if ($reservasi->isEmpty())
                <x-keadaan-kosong judul="Tidak ada reservasi di daftar ini" ikon="daftar">
                    @if ($status === 'pending') Semua pengajuan sudah ditinjau. @else Coba ubah filter status atau tanggal. @endif
                </x-keadaan-kosong>
            @else
                <div class="kartu overflow-x-auto">
                    <table class="tabel min-w-[640px]">
                        <thead class="bg-lembut">
                            <tr><th>Kode booking</th><th>Pengunjung</th><th>Jadwal</th><th>Sisa kursi</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($reservasi as $item)
                                @php
                                    $slot = $item->schedule;
                                    $disetujui = (int) $slot->jumlah_disetujui;
                                    $sisa = max(0, $slot->capacity - $disetujui);
                                    $kurang = $item->status === 'pending' && $item->total_visitors > $sisa;
                                @endphp
                                <tr class="cursor-pointer {{ $dipilih?->id === $item->id ? 'bg-maroon-lembut hover:bg-maroon-lembut' : '' }}"
                                    x-data x-on:click="window.location = @js($urlDengan(['pilih' => $item->id, 'page' => request('page')]))">
                                    <td>
                                        <a href="{{ $urlDengan(['pilih' => $item->id, 'page' => request('page')]) }}" class="whitespace-nowrap font-semibold text-maroon">{{ $item->booking_code }}</a>
                                        <p class="text-cokelat-600">Diajukan {{ $item->created_at->translatedFormat('j M') }}</p>
                                    </td>
                                    <td>
                                        <p class="font-semibold">{{ $item->visitor_name }}</p>
                                        <p class="text-cokelat-600">{{ $item->total_visitors }} orang</p>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $slot->visit_date->translatedFormat('D, j M') }}<br>
                                        <span class="text-cokelat-600">{{ $slot->rentangJam() }}</span>
                                    </td>
                                    <td>
                                        <p class="font-semibold {{ $kurang ? 'text-status-ditolak-teks' : '' }}">{{ $sisa }} dari {{ $slot->capacity }}</p>
                                        <div class="mt-1 h-1 w-20 overflow-hidden rounded-full bg-garis">
                                            <div class="h-full {{ $kurang ? 'bg-status-ditolak-teks' : 'bg-maroon' }}" style="width: {{ min(100, $disetujui / max(1, $slot->capacity) * 100) }}%"></div>
                                        </div>
                                        @if ($kurang)
                                            <p class="text-status-ditolak-teks">kurang</p>
                                        @endif
                                    </td>
                                    <td><x-badge-status :status="$item->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $reservasi->links() }}</div>
            @endif
        </div>

        {{-- Panel detail pengajuan --}}
        @if ($dipilih)
            @php $k = $kapasitas; @endphp
            <aside class="kartu flex flex-col gap-5 self-start p-6 xl:sticky xl:top-24" aria-label="Detail pengajuan">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="teks-label-kecil uppercase tracking-[0.1em] text-cokelat-600">Detail pengajuan</p>
                        <p class="teks-h3 mt-1">{{ $dipilih->booking_code }}</p>
                    </div>
                    <x-badge-status :status="$dipilih->status" />
                </div>
                <p class="text-kecil text-cokelat-600">Diajukan {{ $dipilih->created_at->translatedFormat('j M Y, H.i') }} oleh akun {{ $dipilih->user->name }}</p>

                <dl class="text-kecil">
                    @foreach (['Penanggung jawab' => $dipilih->visitor_name, 'Telepon' => $dipilih->phone, 'Jumlah orang' => $dipilih->total_visitors.' orang', 'Catatan' => $dipilih->notes ?: '—'] as $label => $nilai)
                        <div class="grid grid-cols-[130px_minmax(0,1fr)] gap-3 border-b border-garis py-2.5">
                            <dt class="text-cokelat-600">{{ $label }}</dt><dd class="font-semibold">{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="flex flex-col gap-3 rounded-input bg-lembut p-4 text-kecil">
                    <p class="flex items-center gap-2 text-label font-semibold">
                        <x-ikon nama="kalender" class="size-4" />
                        {{ $dipilih->schedule->visit_date->translatedFormat('D, j M Y') }} · {{ $dipilih->schedule->rentangJam() }}
                    </p>
                    <div class="flex h-2 overflow-hidden rounded-full bg-garis" aria-hidden="true">
                        <span class="h-full bg-maroon" style="width: {{ min(100, $k['disetujui'] / max(1, $k['kapasitas']) * 100) }}%"></span>
                        @if ($dipilih->status === 'pending')
                            <span class="h-full {{ $k['cukup'] ? 'bg-emas' : 'bg-status-ditolak-teks/60' }}" style="width: {{ min(100 - min(100, $k['disetujui'] / max(1, $k['kapasitas']) * 100), $k['pengajuan'] / max(1, $k['kapasitas']) * 100) }}%"></span>
                        @endif
                    </div>
                    <div class="flex justify-between"><span class="text-cokelat-600">Kapasitas slot</span><span class="font-semibold">{{ $k['kapasitas'] }} kursi</span></div>
                    <div class="flex justify-between"><span class="text-cokelat-600">Sudah disetujui</span><span class="font-semibold">{{ $k['disetujui'] }} orang</span></div>
                    <div class="flex justify-between"><span class="text-cokelat-600">Pengajuan ini</span><span class="font-semibold">{{ $k['pengajuan'] }} orang</span></div>
                    @if ($k['cukup'])
                        <div class="flex justify-between"><span class="text-cokelat-600">Sisa setelah disetujui</span><span class="font-semibold text-status-disetujui-teks">{{ $k['sisa'] - $k['pengajuan'] }} kursi</span></div>
                    @else
                        <div class="flex justify-between"><span class="text-cokelat-600">Kekurangan</span><span class="font-semibold text-status-ditolak-teks">{{ $k['kekurangan'] }} kursi</span></div>
                    @endif
                </div>

                @if ($dipilih->status === 'pending')
                    @unless ($k['cukup'])
                        <x-peringatan jenis="galat" judul="Kapasitas slot tidak cukup" :tutup="false">
                            Sisa {{ $k['sisa'] }} kursi, pengajuan ini {{ $k['pengajuan'] }} orang. Tolak pengajuan, atau hubungi pengunjung agar memilih jadwal lain.
                        </x-peringatan>
                    @endunless

                    @php
                        $ringkasan = $dipilih->visitor_name.' · '.$dipilih->total_visitors.' orang · '.$dipilih->schedule->visit_date->translatedFormat('D, j M Y').' · '.$dipilih->schedule->rentangJam();
                    @endphp
                    <div class="grid grid-cols-2 gap-3">
                        <form method="POST" action="{{ route('admin.reservasi.tolak', $dipilih) }}" x-data
                              x-on:submit.prevent="$store.konfirmasi.buka($el, {
                                  judul: @js('Tolak '.$dipilih->booking_code.'?'),
                                  isi: 'Status berubah menjadi Ditolak dan tidak bisa dikembalikan ke Menunggu. Pengunjung tetap bisa mengajukan reservasi baru di jadwal lain.',
                                  ringkasan: @js($ringkasan),
                                  tombol: 'Ya, tolak',
                                  jenis: 'tolak',
                              })">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="tombol tombol-garis w-full">Tolak</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reservasi.setujui', $dipilih) }}" x-data
                              x-on:submit.prevent="$store.konfirmasi.buka($el, {
                                  judul: @js('Setujui '.$dipilih->booking_code.'?'),
                                  isi: @js('Kuota slot berkurang '.$k['pengajuan'].' kursi. Sistem memeriksa ulang sisa kursi saat Anda menekan tombol ini.'),
                                  ringkasan: @js($ringkasan),
                                  tombol: 'Ya, setujui',
                                  jenis: 'setujui',
                              })">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="tombol tombol-utama w-full" @disabled(! $k['cukup'])>Setujui</button>
                        </form>
                    </div>
                    @unless ($k['cukup'])
                        <p class="text-kecil text-cokelat-600">Tombol Setujui nonaktif karena kapasitas tidak cukup.</p>
                    @endunless
                @endif
            </aside>
        @endif
    </div>
@endsection

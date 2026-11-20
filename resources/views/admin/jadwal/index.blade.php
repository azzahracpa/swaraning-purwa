@extends('layouts.admin')

@section('title', 'Jadwal Kunjungan')
@section('halaman', 'Kelola Jadwal')

@php
    $urlPekan = fn ($tanggal) => route('admin.jadwal.index', array_filter(['pekan' => $tanggal->toDateString(), 'status' => $filter]));
    $badge = [
        'dibuka' => ['Dibuka', 'positif', 'centang'],
        'penuh' => ['Penuh', 'negatif', 'peringatan'],
        'ditutup' => ['Ditutup', 'netral', 'larangan'],
    ];
    $slot = $sedangDiubah;
@endphp

@section('content')
    <x-admin.kepala judul="Jadwal" aksen="kunjungan"
                    deskripsi="Buka slot per tanggal dan jam. Kapasitas dihitung dari reservasi yang sudah disetujui; pengajuan Menunggu ditampilkan terpisah.">
        <a href="{{ route('admin.jadwal.index', ['pekan' => $awalPekan->toDateString()]) }}#form-slot" class="tombol tombol-utama">Buka slot baru</a>
    </x-admin.kepala>

    <div class="flex flex-col gap-4 pb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ $urlPekan($awalPekan->copy()->subWeek()) }}" class="tombol-ikon" aria-label="Pekan sebelumnya"><x-ikon nama="panah-kiri" /></a>
            <div>
                <p class="teks-label-kecil uppercase tracking-[0.1em] text-cokelat-600">
                    {{ $awalPekan->isSameWeek(today()) ? 'Pekan ini' : 'Pekan' }}
                </p>
                <p class="teks-h3">{{ $awalPekan->translatedFormat('j') }}–{{ $akhirPekan->translatedFormat('j F Y') }}</p>
            </div>
            <a href="{{ $urlPekan($awalPekan->copy()->addWeek()) }}" class="tombol-ikon" aria-label="Pekan berikutnya"><x-ikon nama="panah-kanan" /></a>
            <x-penanda-contoh teks="data contoh" />
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach (['semua' => 'Semua', 'dibuka' => 'Dibuka', 'penuh' => 'Penuh', 'ditutup' => 'Ditutup'] as $kunci => $label)
                <x-admin.chip :href="route('admin.jadwal.index', array_filter(['pekan' => $awalPekan->toDateString(), 'status' => $kunci === 'semua' ? null : $kunci]))"
                              :aktif="($filter ?? 'semua') === $kunci" :jumlah="$jumlah[$kunci]">{{ $label }}</x-admin.chip>
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <div class="flex flex-col gap-6">
            <div class="flex flex-wrap gap-5 text-kecil text-cokelat-600">
                <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-maroon"></span> Disetujui (mengurangi kuota)</span>
                <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-emas"></span> Menunggu keputusan</span>
                <span class="flex items-center gap-2"><span class="size-2.5 rounded-sm bg-garis"></span> Kursi kosong</span>
            </div>

            @forelse ($slotPerTanggal as $tanggal => $daftarSlot)
                @php
                    $hari = \Illuminate\Support\Carbon::parse($tanggal);
                    $totalKapasitas = $daftarSlot->sum('capacity');
                    $totalDisetujui = $daftarSlot->sum('jumlah_disetujui');
                @endphp
                <section x-data x-muncul>
                    <div class="mb-3 flex items-end gap-4">
                        <span class="font-display text-angka leading-none">{{ $hari->format('j') }}</span>
                        <div>
                            <p class="text-label font-semibold">{{ $hari->translatedFormat('l · F Y') }}</p>
                            <p class="text-kecil text-cokelat-600">{{ $daftarSlot->count() }} slot · {{ $totalDisetujui }} dari {{ $totalKapasitas }} kursi disetujui</p>
                        </div>
                    </div>
                    <div class="kartu divide-y divide-garis">
                        @foreach ($daftarSlot as $item)
                            @php
                                $status = $item->statusSlot();
                                [$label, $nada, $ikon] = $badge[$status];
                                $disetujui = (int) $item->jumlah_disetujui;
                                $menunggu = (int) $item->jumlah_menunggu;
                                $sisa = max(0, $item->capacity - $disetujui);
                            @endphp
                            <div class="flex flex-col gap-3 p-4 transition-colors sm:flex-row sm:items-center sm:gap-5 {{ $slot?->id === $item->id ? 'bg-maroon-lembut' : 'hover:bg-ivory/60' }}">
                                <p class="flex w-32 shrink-0 items-center gap-2 text-label font-semibold">
                                    <x-ikon nama="jam" class="size-4 text-cokelat-600" /> {{ $item->rentangJam() }}
                                </p>
                                <div class="min-w-0 flex-1">
                                    @if ($status === 'ditutup')
                                        <p class="text-kecil text-cokelat-600">Tidak menerima pengajuan baru</p>
                                    @else
                                        {{-- Bar kapasitas: maroon = disetujui, emas = menunggu --}}
                                        <div class="flex h-1.5 w-full max-w-[200px] overflow-hidden rounded-full bg-garis" role="img"
                                             aria-label="{{ $disetujui }} dari {{ $item->capacity }} disetujui, {{ $menunggu }} menunggu">
                                            <span class="h-full {{ $status === 'penuh' ? 'bg-status-ditolak-teks' : 'bg-maroon' }} transition-all duration-700"
                                                  style="width: {{ min(100, $disetujui / max(1, $item->capacity) * 100) }}%"></span>
                                            <span class="h-full bg-emas" style="width: {{ min(100 - min(100, $disetujui / max(1, $item->capacity) * 100), $menunggu / max(1, $item->capacity) * 100) }}%"></span>
                                        </div>
                                        <p class="mt-1.5 text-kecil {{ $status === 'penuh' ? 'text-status-ditolak-teks' : 'text-cokelat-600' }}">
                                            {{ $disetujui }}/{{ $item->capacity }} disetujui
                                            @if ($menunggu) · {{ $menunggu }} menunggu @endif
                                            · sisa {{ $sisa }}
                                        </p>
                                    @endif
                                </div>
                                <x-badge-keadaan :nada="$nada" class="gap-1.5 self-start sm:self-center"><x-ikon :nama="$ikon" class="size-3.5" />{{ $label }}</x-badge-keadaan>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.jadwal.index', ['pekan' => $awalPekan->toDateString(), 'ubah' => $item->id]) }}#form-slot" class="tombol tombol-garis tombol-kecil">Ubah</a>
                                    <form method="POST" action="{{ route('admin.jadwal.status', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="tombol tombol-garis tombol-kecil w-[72px]">{{ $item->is_available ? 'Tutup' : 'Buka' }}</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @empty
                <x-keadaan-kosong judul="Belum ada slot di pekan ini" ikon="kalender">
                    Buka slot baru lewat formulir, atau pindah ke pekan lain dengan tombol panah.
                </x-keadaan-kosong>
            @endforelse

            <p class="flex gap-2 text-kecil text-cokelat-600">
                <x-ikon nama="info" class="size-4 shrink-0" />
                Slot yang sudah memiliki reservasi tidak dapat dihapus. Tutup slot agar tidak menerima pengajuan baru.
            </p>
        </div>

        {{-- Form buka / ubah slot --}}
        <div id="form-slot" class="scroll-mt-24">
            <form method="POST" action="{{ $slot ? route('admin.jadwal.update', $slot) : route('admin.jadwal.store') }}"
                  class="kartu flex flex-col gap-5 p-6 xl:sticky xl:top-24">
                @csrf
                @if ($slot)
                    @method('PUT')
                @endif
                <div>
                    <p class="teks-label-kecil uppercase tracking-[0.1em] text-cokelat-600">{{ $slot ? 'Ubah slot' : 'Slot baru' }}</p>
                    <h2 class="teks-h3 mt-1">{{ $slot ? 'Ubah slot '.$slot->rentangJam() : 'Buka slot kunjungan' }}</h2>
                </div>

                @if ($errors->any())
                    <x-peringatan jenis="galat" judul="Periksa {{ $errors->count() }} isian" :tutup="false">Slot belum disimpan.</x-peringatan>
                @endif

                <x-kolom-isian name="visit_date" label="Tanggal kunjungan" type="date" wajib
                               :value="$slot?->visit_date->toDateString() ?? $awalPekan->copy()->max(today())->toDateString()"
                               :min="today()->toDateString()" />
                <div class="grid grid-cols-2 gap-3">
                    <x-kolom-isian name="start_time" label="Jam mulai" type="time" wajib :value="$slot ? substr($slot->start_time, 0, 5) : '09:00'" />
                    <x-kolom-isian name="end_time" label="Jam selesai" type="time" wajib :value="$slot ? substr($slot->end_time, 0, 5) : '10:00'" />
                </div>
                <x-kolom-isian name="capacity" label="Kapasitas" type="number" min="1" max="500" wajib :value="$slot?->capacity ?? 25"
                               :bantuan="$slot ? 'Sudah disetujui: '.$slot->jumlahDisetujui().' orang.' : 'Jumlah maksimal pengunjung pada slot ini.'" />
                <div class="flex flex-col gap-2">
                    <x-sakelar name="is_available" :checked="(bool) old('is_available', $slot?->is_available ?? true)" label="Dibuka untuk reservasi" label-mati="Ditutup" />
                    <p class="kolom-bantuan">Jika dimatikan, slot tetap tersimpan tetapi tidak muncul di halaman Reservasi pengunjung.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.jadwal.index', ['pekan' => $awalPekan->toDateString()]) }}" class="tombol tombol-garis">Batal</a>
                    <button type="submit" class="tombol tombol-utama">Simpan slot</button>
                </div>

                @if ($slot && ! $slot->reservations()->exists())
                    <button type="submit" form="form-hapus-slot" class="tautan self-start text-status-ditolak-teks">Hapus slot ini</button>
                @endif
            </form>

            @if ($slot)
                <form method="POST" action="{{ route('admin.jadwal.destroy', $slot) }}" id="form-hapus-slot" x-data
                      x-on:submit.prevent="$store.konfirmasi.buka($el, {
                          judul: 'Hapus slot ini?',
                          isi: 'Slot belum memiliki reservasi dan akan dihapus permanen.',
                          ringkasan: @js($slot->visit_date->translatedFormat('l, j F Y').' · '.$slot->rentangJam()),
                          tombol: 'Hapus slot',
                      })">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>
@endsection

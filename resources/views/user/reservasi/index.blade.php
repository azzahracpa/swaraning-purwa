@extends('layouts.user')

@section('title', 'Reservasi Kunjungan')

@php
    // Data slot untuk Alpine: id, jam, sisa kuota, dan apakah bisa dipilih
    $dataSlot = $slot->map(fn ($s) => [
        'id' => $s->id,
        'jam' => $s->rentangJam(),
        'kapasitas' => $s->capacity,
        'sisa' => max(0, $s->capacity - (int) $s->jumlah_disetujui),
        'bisa' => $s->is_available && $s->capacity - (int) $s->jumlah_disetujui > 0,
    ])->values();
    $slotAwal = (int) old('schedule_id') ?: null;
    $tanggalTeks = $tanggalDipilih ? \Illuminate\Support\Carbon::parse($tanggalDipilih)->translatedFormat('l, j M Y') : null;
@endphp

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => route('dashboard'), 'Reservasi Kunjungan' => null]" />
    <h1 class="teks-display-l mt-6 uppercase" x-data x-muncul>Reservasi <span class="aksen-miring">kunjungan</span></h1>
    <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
        Pilih slot yang masih punya kuota, isi data rombongan, lalu kirim. Pengelola akan meninjau pengajuan Anda.
    </p>
@endsection

@section('content')
    @if ($daftarTanggal->isEmpty())
        <x-keadaan-kosong judul="Belum ada jadwal kunjungan yang dibuka" ikon="kalender">
            Pengelola belum membuka slot kunjungan untuk beberapa hari ke depan. Silakan cek kembali nanti.
        </x-keadaan-kosong>
    @else
        <form method="POST" action="{{ route('reservasi.store') }}"
              x-data="{
                  slot: @js($dataSlot),
                  pilih: @js($slotAwal),
                  jumlah: {{ (int) old('total_visitors', 1) }},
                  nama: @js(old('visitor_name', $user->name)),
                  mengirim: false,
                  get terpilih() { return this.slot.find(s => s.id === this.pilih) },
                  get maks() { return this.terpilih ? this.terpilih.sisa : 1 },
                  ubahJumlah(n) { this.jumlah = Math.min(Math.max(1, (Number(this.jumlah) || 0) + n), this.maks) },
              }"
              x-on:submit="mengirim = true"
              class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1fr)_300px]">
            @csrf

            {{-- min-w-0 supaya deretan tanggal yang bisa digeser tidak melebarkan halaman di ponsel --}}
            <div class="flex min-w-0 flex-col gap-10">
                {{-- 01 Pilih jadwal --}}
                <section class="flex flex-col gap-5">
                    <x-label-seksi nomor="01" label="Pilih jadwal" />

                    <div class="flex flex-col gap-3">
                        <p class="flex items-center gap-3 text-label font-semibold">Tanggal kunjungan <x-penanda-contoh teks="jadwal contoh" /></p>
                        <div x-geser class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1">
                            @foreach ($daftarTanggal as $tanggal)
                                @php $aktif = $tanggal->toDateString() === $tanggalDipilih; @endphp
                                <a href="{{ route('reservasi.index', ['tanggal' => $tanggal->toDateString()]) }}"
                                   class="flex min-w-[84px] shrink-0 flex-col items-center rounded-input border px-3 py-2.5 text-center transition
                                          {{ $aktif ? 'border-cokelat bg-cokelat text-ivory' : 'border-garis-kuat bg-putih hover:border-cokelat' }}"
                                   @if ($aktif) aria-current="date" @endif>
                                    <span class="text-kecil {{ $aktif ? 'text-di-gelap' : 'text-cokelat-600' }}">{{ $tanggal->translatedFormat('l') }}</span>
                                    <span class="text-label font-semibold">{{ $tanggal->translatedFormat('j M') }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3" role="radiogroup" aria-label="Slot jam kunjungan">
                        <template x-for="s in slot" :key="s.id">
                            <label class="relative flex flex-col gap-2.5 rounded-input border p-4 transition"
                                   :class="! s.bisa ? 'cursor-not-allowed border-garis bg-lembut opacity-80'
                                         : (pilih === s.id ? 'cursor-pointer border-maroon bg-maroon-lembut ring-1 ring-maroon' : 'cursor-pointer border-garis-kuat bg-putih hover:border-cokelat')">
                                <input type="radio" name="schedule_id" :value="s.id" class="sr-only" :disabled="! s.bisa"
                                       x-on:change="pilih = s.id; jumlah = Math.min(jumlah, s.sisa)" :checked="pilih === s.id">
                                <span class="flex items-center justify-between">
                                    <span class="text-label font-semibold" x-text="s.jam"></span>
                                    <span class="flex size-5 items-center justify-center rounded-full border-2 transition"
                                          :class="pilih === s.id ? 'border-maroon' : 'border-garis-kuat'">
                                        <span class="size-2.5 rounded-full bg-maroon" x-show="pilih === s.id"></span>
                                    </span>
                                </span>
                                <span class="h-1 overflow-hidden rounded-full bg-garis">
                                    <span class="block h-full rounded-full transition-all"
                                          :class="s.bisa ? 'bg-emas' : 'bg-cokelat-600'"
                                          :style="`width: ${((s.kapasitas - s.sisa) / s.kapasitas) * 100}%`"></span>
                                </span>
                                <span class="text-kecil" :class="s.bisa ? 'text-cokelat-600' : 'font-semibold text-status-ditolak-teks'"
                                      x-text="s.bisa ? `Sisa kuota ${s.sisa} dari ${s.kapasitas} orang` : 'Kuota penuh'"></span>
                            </label>
                        </template>
                    </div>
                    @error('schedule_id')
                        <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
                    @else
                        <p class="text-kecil text-cokelat-600">Slot berlabel "Kuota penuh" atau yang ditutup pengelola tidak bisa dipilih.</p>
                    @enderror
                </section>

                {{-- 02 Data rombongan --}}
                <section class="flex flex-col gap-5">
                    <x-label-seksi nomor="02" label="Data rombongan" />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-kolom-isian name="visitor_name" label="Nama penanggung jawab" :value="$user->name" wajib
                                       bantuan="Terisi dari akun Anda, boleh diubah." x-model="nama" />
                        <x-kolom-isian name="phone" label="Nomor telepon" type="tel" :value="$user->phone" wajib
                                       placeholder="0812 3456 7890" bantuan="Dihubungi bila ada perubahan jadwal." />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="total_visitors" class="kolom-label">Jumlah pengunjung <span class="text-maroon" aria-hidden="true">*</span></label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="tombol-ikon" x-on:click="ubahJumlah(-1)" aria-label="Kurangi jumlah"><x-ikon nama="kurang" /></button>
                            <input id="total_visitors" name="total_visitors" type="number" min="1" :max="maks" x-model.number="jumlah" required
                                   class="kolom-input h-11 w-24 text-center @error('total_visitors') kolom-galat @enderror">
                            <button type="button" class="tombol-ikon" x-on:click="ubahJumlah(1)" aria-label="Tambah jumlah"><x-ikon nama="tambah" /></button>
                            <span class="text-kecil text-cokelat-600">orang</span>
                        </div>
                        @error('total_visitors')
                            <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
                        @else
                            <p class="kolom-bantuan" x-text="terpilih ? `Maksimal ${terpilih.sisa} orang untuk slot yang dipilih.` : 'Pilih slot dulu untuk melihat batas jumlah pengunjung.'"></p>
                        @enderror
                    </div>

                    <x-kolom-isian name="notes" label="Catatan" placeholder="Contoh: rombongan pelajar SMP, ada 2 pendamping guru."
                                   bantuan="Opsional. Maksimal 500 karakter." maxlength="500" />

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="tombol tombol-utama" :disabled="mengirim || ! pilih">
                            <span x-show="! mengirim">Kirim pengajuan</span><span x-show="mengirim" x-cloak>Mengirim…</span>
                        </button>
                        <a href="{{ route('dashboard') }}" class="tombol tombol-garis">Batal</a>
                    </div>
                </section>
            </div>

            {{-- Ringkasan pengajuan yang ikut berubah --}}
            <aside class="kartu flex flex-col gap-4 self-start p-5 xl:sticky xl:top-24">
                <div class="flex items-center justify-between">
                    <h2 class="teks-h3">Ringkasan pengajuan</h2>
                    <x-penanda-contoh />
                </div>
                <dl class="flex flex-col text-kecil">
                    <div class="flex justify-between gap-3 border-t border-garis py-2.5"><dt class="text-cokelat-600">Jadwal</dt><dd class="text-right font-semibold">{{ $tanggalTeks }}</dd></div>
                    <div class="flex justify-between gap-3 border-t border-garis py-2.5"><dt class="text-cokelat-600">Jam</dt><dd class="font-semibold" x-text="terpilih ? terpilih.jam : '—'"></dd></div>
                    <div class="flex justify-between gap-3 border-t border-garis py-2.5"><dt class="text-cokelat-600">Jumlah pengunjung</dt><dd class="font-semibold" x-text="jumlah + ' orang'"></dd></div>
                    <div class="flex justify-between gap-3 border-t border-garis py-2.5"><dt class="text-cokelat-600">Penanggung jawab</dt><dd class="text-right font-semibold" x-text="nama || '—'"></dd></div>
                    <div class="flex items-center justify-between gap-3 border-t border-garis py-2.5"><dt class="text-cokelat-600">Status setelah dikirim</dt><dd><x-badge-status status="pending" /></dd></div>
                </dl>
                <div class="flex flex-col gap-2 rounded-input bg-lembut p-3.5 text-kecil">
                    <p>Pengajuan belum mengurangi kuota. Kuota berkurang setelah pengelola menyetujui.</p>
                    <p>Selama status masih Menunggu, Anda dapat membatalkan dari Riwayat Reservasi.</p>
                    <p>Tidak ada pembayaran melalui situs ini.</p>
                </div>
            </aside>
        </form>
    @endif
@endsection

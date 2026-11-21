@extends('layouts.publik')

@section('title', $attempt->quiz->title)

@section('content')
    {{--
        Semua soal dimuat sekali. Jawaban disimpan sementara di perangkat (localStorage)
        supaya tidak hilang saat halaman dimuat ulang, lalu dikirim sekaligus ke server.
        Kunci jawaban tidak pernah dikirim ke browser.
    --}}
    <section class="wadah pb-24 pt-6 lg:pb-32"
             x-data="kerjakanKuis(@js($soal), 'kuis-jawaban-{{ $attempt->id }}')">
        <x-breadcrumb :items="['Beranda' => route('beranda'), 'Kuis Edukasi' => route('kuis.index'), $attempt->quiz->title => null]" />
        <h1 class="teks-display-l mt-6 uppercase">{{ $attempt->quiz->title }}</h1>
        <p class="mt-4 text-isi text-cokelat-600">
            Soal <span x-text="ke + 1"></span> dari {{ count($soal) }}. Jawaban tersimpan otomatis di perangkat ini setiap kali Anda memilih.
        </p>

        @error('jawaban')
            <x-peringatan jenis="galat" judul="Jawaban ditolak" :tutup="false" class="mt-6">{{ $message }}</x-peringatan>
        @enderror

        <form method="POST" action="{{ route('kuis.kirim', $attempt) }}" x-ref="form" class="mt-10 grid gap-8 xl:grid-cols-[minmax(0,1fr)_280px]">
            @csrf
            <template x-for="(id, s) in jawaban" :key="s">
                <input type="hidden" :name="`jawaban[${s}]`" :value="id">
            </template>

            <div class="kartu flex flex-col gap-6 p-5 sm:p-8">
                <div class="flex items-center justify-between gap-3">
                    <p class="flex items-center gap-3 text-label font-semibold">
                        Soal <span x-text="ke + 1"></span> dari {{ count($soal) }} <x-penanda-contoh />
                    </p>
                    <p class="flex items-center gap-1.5 text-kecil text-cokelat-600" x-show="jawaban[soalAktif.id]" x-transition>
                        <x-ikon nama="centang" class="size-4" /> Jawaban tersimpan
                    </p>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-lembut">
                    <div class="h-full rounded-full bg-maroon transition-all duration-500" :style="`width: ${((ke + 1) / soal.length) * 100}%`"></div>
                </div>

                <div class="flex gap-4">
                    <span class="font-display text-angka-sedang text-emas-700" x-text="String(ke + 1).padStart(2, '0')"></span>
                    <h2 class="teks-h2" x-text="soalAktif.teks"></h2>
                </div>

                <div class="flex flex-col gap-3" role="radiogroup" :aria-label="'Pilihan jawaban soal ' + (ke + 1)">
                    <template x-for="(o, i) in soalAktif.opsi" :key="o.id">
                        <button type="button" role="radio" :aria-checked="jawaban[soalAktif.id] === o.id"
                                x-on:click="pilih(o.id)"
                                class="flex min-h-14 items-center gap-4 rounded-input border px-4 py-3 text-left text-isi transition"
                                :class="jawaban[soalAktif.id] === o.id ? 'border-maroon bg-maroon-lembut' : 'border-garis-kuat bg-putih hover:border-cokelat'">
                            <span class="flex size-[30px] shrink-0 items-center justify-center rounded-full border text-label font-semibold transition"
                                  :class="jawaban[soalAktif.id] === o.id ? 'border-maroon bg-maroon text-putih' : 'border-garis-kuat'"
                                  x-text="'ABCDEF'[i]"></span>
                            <span class="flex-1" x-text="o.teks"></span>
                            <span class="text-kecil text-maroon" x-show="jawaban[soalAktif.id] === o.id">Jawaban Anda</span>
                        </button>
                    </template>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <button type="button" class="tombol tombol-garis" :disabled="ke === 0" x-on:click="ke--">Sebelumnya</button>
                    <button type="button" class="tombol tombol-utama" x-show="ke < soal.length - 1" x-on:click="ke++">Berikutnya</button>
                    <button type="button" class="tombol tombol-utama" x-show="ke === soal.length - 1" x-on:click="konfirmasi = true">Selesai dan kirim</button>
                </div>
            </div>

            {{-- Navigasi nomor soal --}}
            <aside class="flex flex-col gap-4 self-start xl:sticky xl:top-24">
                <p class="flex items-center gap-2 text-label font-semibold"><x-belah-ketupat /> Navigasi soal</p>
                <p class="text-kecil text-cokelat-600">Pilih nomor untuk berpindah soal. Jawaban tidak hilang.</p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="(s, i) in soal" :key="s.id">
                        <button type="button" x-on:click="ke = i"
                                class="flex size-11 items-center justify-center rounded-kecil border text-label font-semibold transition"
                                :class="i === ke ? 'border-maroon bg-maroon-lembut text-maroon'
                                      : (jawaban[s.id] ? 'border-cokelat bg-cokelat text-ivory' : 'border-garis-kuat bg-putih hover:border-cokelat')"
                                :aria-label="'Soal ' + (i + 1) + (jawaban[s.id] ? ', sudah dijawab' : ', belum dijawab')"
                                x-text="i + 1"></button>
                    </template>
                </div>
                <ul class="flex flex-col gap-2 text-kecil text-cokelat-600">
                    <li class="flex items-center gap-2"><span class="size-3.5 rounded-sm bg-cokelat"></span> Sudah dijawab</li>
                    <li class="flex items-center gap-2"><span class="size-3.5 rounded-sm border-2 border-maroon bg-maroon-lembut"></span> Sedang dibuka</li>
                    <li class="flex items-center gap-2"><span class="size-3.5 rounded-sm border border-garis-kuat bg-putih"></span> Belum dijawab</li>
                </ul>
                <div class="h-px bg-cokelat-600/40"></div>
                <p class="text-label font-semibold"><span x-text="terjawab"></span> dari {{ count($soal) }} soal sudah dijawab</p>
                <button type="button" class="tombol tombol-garis w-full" x-on:click="konfirmasi = true">Selesai dan kirim</button>
            </aside>

            {{-- Konfirmasi kirim --}}
            <div x-show="konfirmasi" x-cloak class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="judul-kirim">
                <div class="absolute inset-0 bg-cokelat/40" x-on:click="konfirmasi = false" x-transition.opacity></div>
                <div class="relative flex w-full max-w-[460px] flex-col gap-5 rounded-kartu bg-putih p-6 shadow-dialog sm:p-8" x-trap.noscroll="konfirmasi" x-transition>
                    <h2 id="judul-kirim" class="teks-h3">Kirim jawaban sekarang?</h2>
                    <p class="text-isi text-cokelat-600" x-show="terjawab < soal.length">
                        Masih ada <span class="font-semibold text-status-ditolak-teks" x-text="soal.length - terjawab"></span> soal yang belum dijawab dan akan dihitung salah.
                    </p>
                    <p class="text-isi text-cokelat-600" x-show="terjawab === soal.length">Semua soal sudah dijawab. Jawaban tidak bisa diubah setelah dikirim.</p>
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button" class="tombol tombol-garis" x-on:click="konfirmasi = false">Periksa lagi</button>
                        <button type="button" class="tombol tombol-utama" :disabled="mengirim" x-on:click="kirim()">Kirim jawaban</button>
                    </div>
                </div>
            </div>
        </form>

        <a href="{{ route('kuis.index') }}" class="tautan mt-6 inline-flex"><x-ikon nama="panah-kiri" class="size-4" /> Keluar dari kuis</a>
    </section>
@endsection

@push('scripts')
    <script>
        // Logika halaman pengerjaan kuis (Alpine.js)
        function kerjakanKuis(soal, kunciSimpan) {
            let tersimpan = {};
            try { tersimpan = JSON.parse(localStorage.getItem(kunciSimpan) || '{}'); } catch (e) { tersimpan = {}; }

            return {
                soal,
                ke: 0,
                jawaban: tersimpan,
                konfirmasi: false,
                mengirim: false,
                get soalAktif() { return this.soal[this.ke] },
                get terjawab() { return this.soal.filter(s => this.jawaban[s.id]).length },
                pilih(idOpsi) {
                    this.jawaban[this.soalAktif.id] = idOpsi;
                    try { localStorage.setItem(kunciSimpan, JSON.stringify(this.jawaban)); } catch (e) {}
                },
                kirim() {
                    this.mengirim = true;
                    try { localStorage.removeItem(kunciSimpan); } catch (e) {}
                    this.$nextTick(() => this.$refs.form.submit());
                },
                init() {
                    // Tombol panah kiri/kanan untuk pindah soal
                    window.addEventListener('keydown', (e) => {
                        if (this.konfirmasi || ['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
                        if (e.key === 'ArrowRight' && this.ke < this.soal.length - 1) this.ke++;
                        if (e.key === 'ArrowLeft' && this.ke > 0) this.ke--;
                    });
                },
            };
        }
    </script>
@endpush

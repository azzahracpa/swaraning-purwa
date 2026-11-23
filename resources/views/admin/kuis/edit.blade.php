@extends('layouts.admin')

@section('title', $quiz->exists ? 'Ubah Kuis' : 'Buat Kuis')
@section('halaman', 'Kelola Kuis › '.($quiz->exists ? $quiz->title : 'Buat'))

@php
    // Data awal editor: dari old() kalau validasi gagal, atau dari database.
    // Kunci jawaban (is_correct) disembunyikan dari toArray, jadi diambil manual di sini.
    $soalAwal = old('soal') !== null
        ? collect(old('soal'))->values()->map(fn ($s) => [
            'id' => $s['id'] ?? null,
            'question_text' => $s['question_text'] ?? '',
            'points' => (int) ($s['points'] ?? 10),
            'benar' => isset($s['benar']) ? (int) $s['benar'] : null,
            'opsi' => collect($s['opsi'] ?? [])->values()->map(fn ($o) => ['id' => $o['id'] ?? null, 'option_text' => $o['option_text'] ?? ''])->all(),
        ])
        : $quiz->questions->map(fn ($soal) => [
            'id' => $soal->id,
            'question_text' => $soal->question_text,
            'points' => $soal->points,
            'benar' => $soal->options->values()->search(fn ($o) => $o->is_correct) === false ? null : $soal->options->values()->search(fn ($o) => $o->is_correct),
            'opsi' => $soal->options->map(fn ($o) => ['id' => $o->id, 'option_text' => $o->option_text])->values()->all(),
        ]);

    if ($soalAwal->isEmpty()) {
        $soalAwal = collect([[
            'id' => null, 'question_text' => '', 'points' => 10, 'benar' => null,
            'opsi' => [['id' => null, 'option_text' => ''], ['id' => null, 'option_text' => '']],
        ]]);
    }

    $sudahDikerjakan = ($quiz->attempts_count ?? 0) > 0;
@endphp

@section('content')
    <form method="POST" action="{{ $quiz->exists ? route('admin.kuis.update', $quiz) : route('admin.kuis.store') }}"
          x-data="editorKuis(@js($soalAwal), @js($errors->getMessages()), @js(old('title', $quiz->title ?? '')), @js((bool) old('is_active', $quiz->is_active)))"
          x-on:submit="mengirim = true">
        @csrf
        @if ($quiz->exists)
            @method('PUT')
        @endif

        <x-admin.kepala :judul="$quiz->exists ? 'Ubah' : 'Buat'" aksen="kuis"
                        :deskripsi="$quiz->exists ? $quiz->title.' · '.$quiz->questions->count().' soal · '.$quiz->questions->sum('points').' poin. Perubahan tersimpan sebagai Nonaktif sampai syarat terpenuhi.' : 'Isi judul, lalu tambahkan soal dan pilihan jawaban. Kuis bisa diaktifkan setelah syarat terpenuhi.'">
            <a href="{{ route('admin.kuis.index') }}" class="tombol tombol-garis">Batal</a>
            <button type="submit" class="tombol tombol-utama" :disabled="mengirim">Simpan</button>
        </x-admin.kepala>

        @error('soal')
            <x-peringatan jenis="galat" judul="Kuis belum disimpan" :tutup="false" class="mb-6">{{ $message }}</x-peringatan>
        @enderror
        @if ($sudahDikerjakan)
            <x-peringatan jenis="info" judul="Kuis sudah dikerjakan {{ $quiz->attempts_count }} kali" :tutup="false" class="mb-6">
                Soal dan pilihan lama tidak bisa dihapus agar riwayat jawaban pengguna tetap utuh. Teksnya tetap bisa diubah.
            </x-peringatan>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div class="flex flex-col gap-6">
                <x-admin.kotak nomor="01" label="Informasi kuis">
                    <x-kolom-isian name="title" label="Judul kuis" :value="$quiz->title" wajib maxlength="255" x-model="judul" />
                    <x-kolom-isian name="description" label="Deskripsi" type="textarea" rows="2" :value="$quiz->description" />
                </x-admin.kotak>

                <div class="flex items-center justify-between">
                    <x-label-seksi nomor="02" label="Soal dan pilihan jawaban" />
                    <span class="text-kecil text-cokelat-600" x-text="soal.length + ' soal · ' + totalPoin + ' poin'"></span>
                </div>

                <template x-for="(s, i) in soal" :key="s.kunci">
                    <section class="kartu overflow-hidden transition" :class="lengkap(s) ? '' : 'border-status-ditolak-teks'">
                        {{-- Kepala soal (klik untuk buka/tutup) --}}
                        <div class="flex items-center gap-4 p-5">
                            <button type="button" class="flex min-w-0 flex-1 items-center gap-4 text-left" x-on:click="s.buka = ! s.buka" :aria-expanded="s.buka">
                                <span class="font-display text-angka-sedang text-emas-700" x-text="String(i + 1).padStart(2, '0')"></span>
                                <span class="min-w-0">
                                    <span class="block truncate text-label font-semibold" x-text="s.question_text || 'Soal ' + (i + 1)"></span>
                                    <span class="block text-kecil" :class="lengkap(s) ? 'text-cokelat-600' : 'text-status-ditolak-teks'" x-text="ringkasan(s)"></span>
                                </span>
                            </button>
                            <span class="inline-flex h-7 items-center gap-1.5 rounded-kecil border px-2.5 text-label font-semibold"
                                  :class="lengkap(s) ? 'border-status-disetujui-teks/35 bg-status-disetujui-latar text-status-disetujui-teks' : 'border-status-ditolak-teks/35 bg-status-ditolak-latar text-status-ditolak-teks'"
                                  x-text="lengkap(s) ? 'Lengkap' : 'Belum lengkap'"></span>
                            <button type="button" class="tombol-ikon size-9" x-show="! (dikerjakan && s.id)" x-on:click="hapusSoal(i)" :aria-label="'Hapus soal ' + (i + 1)">
                                <x-ikon nama="tutup" class="size-4" />
                            </button>
                            <x-ikon nama="chevron-bawah" class="size-5 shrink-0 text-cokelat-600 transition-transform" x-bind:class="s.buka && 'rotate-180'" />
                        </div>

                        {{-- Isi soal --}}
                        <input type="hidden" :name="`soal[${i}][id]`" :value="s.id ?? ''">
                        <div x-show="s.buka" x-transition.opacity.duration.200ms class="flex flex-col gap-5 border-t border-garis p-5">
                            <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_96px]">
                                <div class="flex flex-col gap-2">
                                    <label class="kolom-label" :for="`teks-${s.kunci}`">Pertanyaan <span class="text-maroon">*</span></label>
                                    <input :id="`teks-${s.kunci}`" class="kolom-input" :name="`soal[${i}][question_text]`" x-model="s.question_text" required maxlength="1000"
                                           :class="galat(`soal.${i}.question_text`) && 'kolom-galat'" placeholder="Tulis pertanyaan">
                                    <p class="kolom-pesan-galat" x-show="galat(`soal.${i}.question_text`)" x-text="galat(`soal.${i}.question_text`)"></p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="kolom-label" :for="`poin-${s.kunci}`">Poin <span class="text-maroon">*</span></label>
                                    <input :id="`poin-${s.kunci}`" type="number" min="1" max="100" class="kolom-input" :name="`soal[${i}][points]`" x-model.number="s.points" required>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <p class="kolom-label">Pilihan jawaban</p>
                                    <p class="text-kecil text-cokelat-600">Klik lingkaran untuk menandai jawaban benar</p>
                                </div>
                                <input type="hidden" :name="`soal[${i}][benar]`" :value="s.benar ?? ''">
                                <template x-for="(o, j) in s.opsi" :key="o.kunci">
                                    <div class="flex items-center gap-3">
                                        <button type="button" x-on:click="s.benar = j" class="flex shrink-0 items-center gap-2 text-kecil"
                                                :aria-pressed="s.benar === j" :aria-label="'Tandai pilihan ' + 'ABCDEF'[j] + ' sebagai jawaban benar'">
                                            <span class="flex size-5 items-center justify-center rounded-full border-2 transition"
                                                  :class="s.benar === j ? 'border-status-disetujui-teks bg-status-disetujui-teks' : 'border-garis-kuat'">
                                                <x-ikon nama="centang" class="size-3 text-putih" x-show="s.benar === j" />
                                            </span>
                                            <span class="hidden w-20 text-left sm:block" :class="s.benar === j ? 'font-semibold text-status-disetujui-teks' : 'text-cokelat-600'"
                                                  x-text="s.benar === j ? 'Benar' : 'Tandai benar'"></span>
                                        </button>
                                        <input type="hidden" :name="`soal[${i}][opsi][${j}][id]`" :value="o.id ?? ''">
                                        <input class="kolom-input h-11 flex-1" :name="`soal[${i}][opsi][${j}][option_text]`" x-model="o.option_text" required maxlength="500"
                                               :placeholder="'Pilihan ' + 'ABCDEF'[j]">
                                        <button type="button" class="tombol-ikon size-11" x-show="! (dikerjakan && o.id)" x-on:click="hapusOpsi(s, j)" :aria-label="'Hapus pilihan ' + 'ABCDEF'[j]">
                                            <x-ikon nama="tutup" class="size-4" />
                                        </button>
                                    </div>
                                </template>
                                <p class="kolom-pesan-galat" x-show="s.opsi.length < 2"><x-ikon nama="peringatan" class="size-4" /> Tambahkan minimal dua pilihan.</p>
                                <p class="kolom-pesan-galat" x-show="s.opsi.length >= 2 && (s.benar === null || s.benar >= s.opsi.length)"><x-ikon nama="peringatan" class="size-4" /> Tandai tepat satu jawaban yang benar.</p>
                                <button type="button" class="flex items-center gap-2 self-start text-label font-semibold text-maroon disabled:opacity-40"
                                        x-on:click="tambahOpsi(s)" :disabled="s.opsi.length >= 6">
                                    <x-ikon nama="tambah" class="size-4" /> Tambah pilihan
                                </button>
                            </div>
                        </div>
                    </section>
                </template>

                <button type="button" x-on:click="tambahSoal()"
                        class="flex h-14 items-center justify-center gap-2 rounded-kartu border border-dashed border-garis-kuat text-label font-semibold transition hover:border-cokelat hover:bg-putih">
                    <x-ikon nama="tambah" class="size-4" /> Tambah soal
                </button>
            </div>

            {{-- Syarat aktif dan ringkasan --}}
            <div class="flex flex-col gap-4 xl:sticky xl:top-24 xl:self-start">
                <div class="kartu flex flex-col gap-4 p-5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-label font-semibold">Syarat sebelum diaktifkan</p>
                        <p class="text-kecil font-semibold" :class="syaratOk === syarat.length ? 'text-status-disetujui-teks' : 'text-status-ditolak-teks'"
                           x-text="syaratOk + ' dari ' + syarat.length + ' terpenuhi'"></p>
                    </div>
                    <ul class="flex flex-col gap-3">
                        <template x-for="sy in syarat" :key="sy.label">
                            <li class="flex gap-2.5 text-kecil">
                                <span class="mt-0.5 flex size-4 shrink-0 items-center justify-center rounded-full"
                                      :class="sy.ok ? 'bg-status-disetujui-latar text-status-disetujui-teks' : 'bg-status-ditolak-latar text-status-ditolak-teks'">
                                    <x-ikon nama="centang" class="size-3" x-show="sy.ok" />
                                    <x-ikon nama="tutup" class="size-3" x-show="! sy.ok" />
                                </span>
                                <span>
                                    <span x-text="sy.label"></span>
                                    <span class="block text-status-ditolak-teks" x-show="! sy.ok && sy.catatan" x-text="sy.catatan"></span>
                                </span>
                            </li>
                        </template>
                    </ul>
                    <div class="h-px bg-garis"></div>
                    <label class="flex items-center gap-3" :class="syaratOk === syarat.length ? 'cursor-pointer' : 'cursor-not-allowed opacity-60'">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" x-model="aktif" :disabled="syaratOk !== syarat.length">
                        <span class="relative h-6 w-11 shrink-0 rounded-full transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-cokelat" :class="aktif && syaratOk === syarat.length ? 'bg-maroon' : 'bg-garis-kuat'">
                            <span class="absolute left-0.5 top-0.5 size-5 rounded-full bg-putih shadow transition-transform" :class="aktif && syaratOk === syarat.length && 'translate-x-5'"></span>
                        </span>
                        <span class="text-label font-semibold">Tampilkan kuis untuk pengguna</span>
                    </label>
                    <p class="text-kecil text-cokelat-600" x-show="syaratOk !== syarat.length">
                        Sakelar terkunci sampai semua syarat terpenuhi. Kuis tetap tersimpan sebagai Nonaktif.
                    </p>
                </div>

                <dl class="flex flex-col gap-2 rounded-kartu bg-lembut p-5 text-kecil">
                    <div class="flex justify-between"><dt class="text-cokelat-600">Jumlah soal</dt><dd class="font-semibold" x-text="soal.length"></dd></div>
                    <div class="flex justify-between"><dt class="text-cokelat-600">Total poin</dt><dd class="font-semibold" x-text="totalPoin"></dd></div>
                    <div class="flex justify-between"><dt class="text-cokelat-600">Pernah dikerjakan</dt><dd class="font-semibold">{{ $sudahDikerjakan ? $quiz->attempts_count.' kali' : 'Belum ada' }}</dd></div>
                </dl>

                @if ($quiz->exists && ! $sudahDikerjakan)
                    <button type="submit" form="form-hapus-kuis" class="tautan self-start text-status-ditolak-teks">Hapus kuis ini</button>
                @endif
            </div>
        </div>
    </form>

    @if ($quiz->exists && ! $sudahDikerjakan)
        <form method="POST" action="{{ route('admin.kuis.destroy', $quiz) }}" id="form-hapus-kuis" x-data
              x-on:submit.prevent="$store.konfirmasi.buka($el, {
                  judul: 'Hapus kuis ini?',
                  isi: 'Semua soal dan pilihan jawabannya ikut terhapus.',
                  ringkasan: @js($quiz->title),
                  tombol: 'Hapus permanen',
              })">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        // Logika editor kuis: tambah/hapus soal dan pilihan, cek syarat aktif secara langsung.
        // Penilaian dan kunci jawaban tetap ditentukan server saat disimpan.
        function editorKuis(soalAwal, galatServer, judulAwal, aktifAwal) {
            let kunci = 0;
            const beriKunci = (s) => ({
                ...s,
                kunci: ++kunci,
                buka: false,
                opsi: s.opsi.map(o => ({ ...o, kunci: ++kunci })),
            });

            const daftar = soalAwal.map(beriKunci);
            // Buka soal pertama yang belum lengkap supaya langsung terlihat
            const belumLengkap = daftar.find(s => s.opsi.length < 2 || s.benar === null || ! s.question_text);
            (belumLengkap ?? daftar[0]).buka = true;

            return {
                soal: daftar,
                judul: judulAwal,
                aktif: aktifAwal,
                mengirim: false,
                dikerjakan: {{ $sudahDikerjakan ? 'true' : 'false' }},
                galat(kunciGalat) { return (galatServer[kunciGalat] ?? [])[0] ?? '' },
                lengkap(s) { return s.question_text.trim() && s.points > 0 && s.opsi.length >= 2 && s.benar !== null && s.benar < s.opsi.length },
                ringkasan(s) {
                    if (s.opsi.length < 2) return 'Baru ' + s.opsi.length + ' pilihan jawaban. Tambahkan minimal satu lagi.';
                    if (s.benar === null || s.benar >= s.opsi.length) return s.opsi.length + ' pilihan · jawaban benar belum ditandai';
                    return s.opsi.length + ' pilihan · jawaban benar: ' + 'ABCDEF'[s.benar] + ' · ' + s.points + ' poin';
                },
                get totalPoin() { return this.soal.reduce((n, s) => n + (Number(s.points) || 0), 0) },
                get syarat() {
                    const nomor = (f) => this.soal.map((s, i) => f(s) ? i + 1 : null).filter(Boolean);
                    const kurang = nomor(s => s.opsi.length < 2);
                    const kunciSalah = nomor(s => s.benar === null || s.benar >= s.opsi.length);
                    const poinNol = nomor(s => ! (s.points > 0));
                    return [
                        { label: 'Judul kuis terisi', ok: !! this.judul.trim() },
                        { label: 'Kuis punya minimal satu soal', ok: this.soal.length > 0 },
                        { label: 'Poin setiap soal lebih dari 0', ok: ! poinNol.length, catatan: 'Soal ' + poinNol.join(', ') + ' belum punya poin.' },
                        { label: 'Setiap soal punya sedikitnya 2 pilihan', ok: this.soal.length > 0 && ! kurang.length, catatan: 'Soal ' + kurang.join(', ') + ' kurang dari 2 pilihan.' },
                        { label: 'Setiap soal punya tepat 1 jawaban benar', ok: this.soal.length > 0 && ! kunciSalah.length, catatan: 'Soal ' + kunciSalah.join(', ') + ' belum ditandai.' },
                    ];
                },
                get syaratOk() { return this.syarat.filter(s => s.ok).length },
                tambahSoal() {
                    this.soal.forEach(s => s.buka = false);
                    this.soal.push(beriKunci({ id: null, question_text: '', points: 10, benar: null, opsi: [{ id: null, option_text: '' }, { id: null, option_text: '' }] }));
                    this.soal[this.soal.length - 1].buka = true;
                },
                hapusSoal(i) { this.soal.splice(i, 1) },
                tambahOpsi(s) { if (s.opsi.length < 6) s.opsi.push({ id: null, option_text: '', kunci: ++kunci }) },
                hapusOpsi(s, j) {
                    s.opsi.splice(j, 1);
                    // Geser tanda jawaban benar mengikuti urutan baru
                    if (s.benar === j) s.benar = null;
                    else if (s.benar !== null && s.benar > j) s.benar--;
                },
            };
        }
    </script>
@endpush

{{--
    Form artikel dipakai bersama oleh halaman tulis (create) dan ubah (edit).
    Isi artikel: paragraf dipisah baris kosong, baris yang diawali "## " menjadi subjudul.
--}}
@php
    $statusAwal = old('status', match ($article->exists ? $article->status() : 'draf') {
        'terbit' => 'terbit',
        'terjadwal' => 'jadwal',
        default => 'draf',
    });
    $tanggalAwal = old('tanggal_terbit', $article->published_at?->format('Y-m-d') ?? now()->addDay()->format('Y-m-d'));
    $jamAwal = old('jam_terbit', $article->published_at?->format('H:i') ?? '09:00');
@endphp

<x-admin.kepala :judul="$judulHalaman" aksen="artikel"
                deskripsi="Penulis diisi otomatis dari akun yang sedang masuk. Kolom bertanda * wajib diisi.">
    @if ($article->exists && $article->sudahTerbit())
        <a href="{{ route('artikel.show', $article) }}" class="tombol tombol-garis" target="_blank">Lihat di situs</a>
    @endif
    <button type="submit" class="tombol tombol-utama">Simpan</button>
</x-admin.kepala>

@if ($errors->any())
    <x-peringatan jenis="galat" judul="Artikel belum disimpan" :tutup="false" class="mb-6">
        Periksa {{ $errors->count() }} isian yang ditandai, lalu simpan lagi.
    </x-peringatan>
@endif

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
    {{-- Kolom kiri: editor --}}
    <div class="kartu flex flex-col"
         x-data="{
            tab: 'tulis',
            isi: @js(old('content', $article->content ?? '')),
            get kata() { return this.isi.trim() ? this.isi.trim().split(/\s+/).length : 0 },
            get blok() {
                return this.isi.split(/\n\s*\n/).map(t => t.trim()).filter(Boolean)
                    .map(t => t.startsWith('## ') ? { sub: true, teks: t.slice(3) } : { sub: false, teks: t });
            },
            sisip(teks) {
                const el = this.$refs.isi;
                const awal = el.selectionStart, akhir = el.selectionEnd;
                this.isi = this.isi.slice(0, awal) + teks + this.isi.slice(akhir);
                this.$nextTick(() => { el.focus(); el.selectionStart = el.selectionEnd = awal + teks.length; });
            },
         }">
        <div class="flex gap-6 border-b border-garis px-6" role="tablist">
            <button type="button" role="tab" :aria-selected="tab === 'tulis'" x-on:click="tab = 'tulis'"
                    class="-mb-px border-b-2 py-4 text-label font-semibold transition"
                    :class="tab === 'tulis' ? 'border-maroon text-maroon' : 'border-transparent text-cokelat-600 hover:text-cokelat'">Tulis</button>
            <button type="button" role="tab" :aria-selected="tab === 'pratinjau'" x-on:click="tab = 'pratinjau'"
                    class="-mb-px border-b-2 py-4 text-label font-semibold transition"
                    :class="tab === 'pratinjau' ? 'border-maroon text-maroon' : 'border-transparent text-cokelat-600 hover:text-cokelat'">Pratinjau</button>
        </div>

        <div class="flex flex-col gap-5 p-6">
            <x-kolom-isian name="title" label="Judul" :value="$article->title" wajib maxlength="255" />

            <div class="flex flex-col gap-2" x-show="tab === 'tulis'">
                <label for="content" class="kolom-label">Isi artikel <span class="text-maroon" aria-hidden="true">*</span></label>
                <div class="flex flex-wrap items-center gap-1 rounded-input bg-lembut p-1.5">
                    <button type="button" x-on:click="sisip('\n\n## Subjudul\n\n')" class="rounded-kecil px-3 py-1.5 text-label font-semibold transition hover:bg-putih">H2 Subjudul</button>
                    <button type="button" x-on:click="sisip('\n\n')" class="rounded-kecil px-3 py-1.5 text-label font-semibold transition hover:bg-putih">¶ Paragraf baru</button>
                    <span class="ml-auto px-2 text-kecil text-cokelat-600">Pisahkan paragraf dengan baris kosong</span>
                </div>
                <textarea id="content" name="content" x-ref="isi" x-model="isi" rows="16"
                          class="kolom-input min-h-[360px] font-sans leading-[26px] @error('content') kolom-galat @enderror"
                          placeholder="Tulis paragraf pembuka artikel…" required></textarea>
                @error('content')
                    <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
                @enderror
            </div>

            {{-- Pratinjau sederhana, sama seperti tampilan di halaman baca artikel --}}
            <div x-show="tab === 'pratinjau'" x-cloak class="flex min-h-[360px] flex-col gap-4 rounded-input border border-garis bg-ivory/50 p-6">
                <template x-if="! blok.length">
                    <p class="text-kecil text-cokelat-600">Belum ada isi untuk ditampilkan.</p>
                </template>
                <template x-for="(b, i) in blok" :key="i">
                    <div>
                        <h2 x-show="b.sub" class="teks-h2" x-text="b.teks"></h2>
                        <p x-show="! b.sub" class="whitespace-pre-line text-isi" x-text="b.teks"></p>
                    </div>
                </template>
            </div>

            <p class="flex justify-between text-kecil text-cokelat-600">
                <span><span x-text="kata"></span> kata · sekitar <span x-text="Math.max(1, Math.ceil(kata / 200))"></span> menit baca</span>
                @if ($article->exists)
                    <span>Terakhir diubah {{ $article->updated_at->translatedFormat('j M Y, H.i') }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Kolom kanan: status terbit, gambar sampul, penulis --}}
    <div class="flex flex-col gap-6">
        {{-- x-data ditaruh di div biasa karena @js() tidak diproses di atribut komponen Blade --}}
        <div x-data="{ status: @js($statusAwal) }">
        <x-admin.kotak>
            <div class="flex items-center justify-between">
                <p class="text-label font-semibold">Status terbit</p>
                <span class="text-kecil text-cokelat-600" x-text="{ draf: 'Draf', terbit: 'Terbit', jadwal: 'Terjadwal' }[status]"></span>
            </div>
            @foreach ([
                'draf' => ['Simpan sebagai draf', 'Hanya terlihat oleh pengelola.'],
                'terbit' => ['Terbitkan sekarang', 'Langsung tampil di halaman Artikel.'],
                'jadwal' => ['Jadwalkan', 'Tampil otomatis pada tanggal terbit.'],
            ] as $nilai => [$judul, $ket])
                <label class="flex cursor-pointer items-start gap-3 rounded-input border p-3.5 transition"
                       :class="status === '{{ $nilai }}' ? 'border-maroon bg-maroon-lembut' : 'border-garis hover:border-garis-kuat'">
                    <input type="radio" name="status" value="{{ $nilai }}" x-model="status" class="mt-0.5 border-garis-kuat text-maroon focus:ring-maroon">
                    <span>
                        <span class="block text-label font-semibold">{{ $judul }}</span>
                        <span class="block text-kecil text-cokelat-600">{{ $ket }}</span>
                    </span>
                </label>
            @endforeach

            <div x-show="status === 'jadwal'" x-cloak x-transition class="grid grid-cols-[minmax(0,1fr)_96px] gap-3">
                <x-kolom-isian name="tanggal_terbit" label="Tanggal terbit" type="date" :value="$tanggalAwal" />
                <x-kolom-isian name="jam_terbit" label="Jam" type="time" :value="$jamAwal" />
            </div>
        </x-admin.kotak>
        </div>

        <x-admin.kotak>
            <x-unggah-gambar name="image" label="Gambar sampul" :url-sekarang="$article->gambar_url" />
        </x-admin.kotak>

        <div class="flex items-center gap-3 rounded-kartu bg-lembut p-4">
            <x-ikon nama="pengguna" class="text-cokelat-600" />
            <div>
                <p class="text-kecil text-cokelat-600">Penulis</p>
                <p class="text-label font-semibold">{{ $article->author?->name ?? auth()->user()->name }}</p>
            </div>
        </div>
    </div>
</div>

<div class="kartu mt-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-end">
    @if ($article->exists)
        <button type="submit" form="form-hapus-artikel" class="tombol tombol-garis sm:mr-auto">Hapus artikel</button>
    @endif
    <a href="{{ route('admin.artikel.index') }}" class="tombol tombol-garis">Batal</a>
    <button type="submit" class="tombol tombol-utama">Simpan</button>
</div>

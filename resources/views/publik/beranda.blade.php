@extends('layouts.publik')

@section('title', 'Beranda')

@php use App\Support\Navigasi; @endphp

@section('content')
    {{-- ================= 1. Hero ================= --}}
    <section class="wadah pt-6 lg:pt-10">
        <div class="flex items-center gap-7" x-data x-muncul>
            <h1 class="teks-display-xl uppercase">
                Swaraning
                <span class="sr-only">Purwa</span>
            </h1>
            <div class="relative hidden h-24 w-[220px] items-center justify-center overflow-hidden rounded-full bg-cokelat xl:flex" aria-hidden="true">
                <span class="absolute inset-0 bg-kawung bg-[length:40px_40px] opacity-[0.16]"></span>
                <x-penanda-contoh latar="gelap" teks="Foto contoh" class="relative" />
            </div>
            <div class="relative hidden h-24 w-[72px] overflow-hidden rounded-b-[10px] rounded-t-[36px] bg-foto xl:block" aria-hidden="true">
                <span class="absolute inset-0 bg-kawung bg-[length:40px_40px] opacity-[0.18]"></span>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-4 lg:flex-row lg:items-end lg:justify-end lg:gap-10" x-data x-muncul="120">
            <div class="flex flex-col gap-2 lg:w-80 lg:pb-[18px]">
                <p class="font-display text-h2-ponsel font-medium italic lg:text-h2">suara dari masa lalu</p>
                <p class="max-w-[300px] text-kecil text-cokelat-600">
                    Mengenal sejarah, menjaga warisan budaya Nusantara di Kota Malang.
                </p>
            </div>
            <p class="teks-display-xl self-end uppercase text-maroon" aria-hidden="true">Purwa</p>
        </div>

        <div class="relative mt-4 h-[220px] overflow-hidden rounded-banner bg-cokelat sm:h-[320px] lg:mt-0 lg:h-[440px]" x-data x-muncul="200">
            @if ($profil?->gambar_url)
                <img src="{{ $profil->gambar_url }}" alt="Foto contoh suasana museum"
                     class="size-full object-cover transition duration-700 hover:scale-[1.02]">
            @else
                <x-tempat-foto class="size-full rounded-none" keterangan="Foto suasana ruang koleksi museum." />
            @endif
        </div>
        <p class="mt-3 text-kecil text-cokelat-600">
            Foto suasana ruang koleksi museum. Semua foto di halaman ini contoh dan menunggu izin penggunaan.
        </p>
    </section>

    {{-- ================= 2. Tentang situs + info kunjungan ================= --}}
    <section class="wadah grid gap-10 pt-16 lg:grid-cols-[minmax(0,1fr)_400px] lg:gap-24 lg:pt-[140px]">
        <div class="flex flex-col gap-6" x-data x-muncul>
            <x-label-seksi nomor="01" label="Tentang situs" />
            <h2 class="teks-h1 max-w-[680px]">Mengenal sejarah, <em class="aksen-miring font-semibold">menjaga warisan</em></h2>
            <p class="teks-lead max-w-[640px] text-cokelat-600">
                Swaraning Purwa adalah media informasi dan edukasi digital Museum Mpu Purwa Kota Malang
                untuk mengenalkan nilai sejarah dan budaya kepada masyarakat.
            </p>
            <div class="flex flex-col gap-5 pt-2 sm:flex-row sm:items-center sm:gap-7">
                <a href="{{ Navigasi::url('koleksi.index') }}" class="tombol tombol-utama">Buka katalog koleksi</a>
                <a href="{{ route('profil-museum') }}" class="tautan self-start sm:self-auto">Profil museum</a>
            </div>
        </div>

        <aside class="kartu flex flex-col gap-[18px] self-start p-5 sm:p-7" x-data x-muncul="150" aria-label="Info kunjungan">
            <div class="flex items-center justify-between">
                <h3 class="text-label font-semibold">Info kunjungan</h3>
                <x-penanda-contoh />
            </div>
            @foreach ([
                ['jam', 'Jam buka', $profil?->opening_hours ?? 'Menunggu verifikasi dari pengelola'],
                ['lokasi', 'Lokasi', $profil?->address ?? 'Kota Malang. Alamat lengkap menyusul'],
                ['kalender', 'Reservasi', 'Diajukan lewat akun, ditinjau pengelola'],
            ] as [$ikon, $judul, $isi])
                <div class="flex items-start gap-3">
                    <x-ikon :nama="$ikon" class="mt-px text-maroon" />
                    <div class="flex flex-col gap-0.5">
                        <p class="text-label font-semibold">{{ $judul }}</p>
                        <p class="text-kecil text-cokelat-600">{{ $isi }}</p>
                    </div>
                </div>
            @endforeach
            <div class="h-px bg-garis"></div>
            <a href="{{ Navigasi::url('reservasi.index') }}" class="tautan self-start">Lihat jadwal tersedia</a>
        </aside>
    </section>

    {{-- ================= 3. Di situs ini ================= --}}
    @php
        $indeks = [
            ['Katalog koleksi', 'Cari arca dan prasasti, saring per kategori.', 'koleksi.index'],
            ['Artikel edukasi', 'Bacaan pendek tentang sejarah dan koleksi.', 'artikel.index'],
            ['Reservasi kunjungan', 'Ajukan jadwal dan pantau statusnya.', 'reservasi.index'],
            ['Kuis edukasi', 'Uji pemahaman setelah membaca.', 'kuis.index'],
            ['Profil museum', 'Sejarah, kontak, dan jam buka.', 'profil-museum'],
        ];
    @endphp
    <nav class="wadah pt-16 lg:pt-[104px]" aria-label="Di situs ini">
        <div class="grid border-y border-cokelat sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($indeks as $i => [$judul, $isi, $rute])
                <a href="{{ Navigasi::url($rute) }}"
                   class="group relative flex gap-4 border-b border-garis py-4 pr-8 transition-colors last:border-b-0 hover:bg-putih/70 sm:flex-col sm:gap-2.5 sm:py-6 lg:border-b-0 lg:border-l lg:px-6 lg:first:border-l-0 lg:first:pl-0"
                   x-data x-muncul="{{ $i * 80 }}">
                    <span class="font-display text-angka-sedang text-emas-700">0{{ $i + 1 }}</span>
                    <span class="flex flex-col gap-1 sm:gap-2.5">
                        <span class="text-label font-semibold transition-colors group-hover:text-maroon sm:font-display sm:text-h3">{{ $judul }}</span>
                        <span class="max-w-[220px] text-kecil text-cokelat-600">{{ $isi }}</span>
                    </span>
                    <x-ikon nama="panah-kanan" class="absolute right-2 top-5 -translate-x-2 text-maroon opacity-0 transition-all duration-200 group-hover:translate-x-0 group-hover:opacity-100 sm:top-6" />
                </a>
            @endforeach
        </div>
    </nav>

    {{-- ================= 4. Koleksi unggulan ================= --}}
    <section class="wadah pt-20 lg:pt-32">
        <div class="flex flex-col gap-5 border-b border-garis pb-8 lg:grid lg:grid-cols-[304px_minmax(0,1fr)_208px] lg:items-end lg:gap-6 lg:pb-12">
            <x-label-seksi nomor="02" label="Koleksi unggulan" class="lg:pb-3" />
            <h2 class="teks-display-l uppercase" x-data x-muncul>
                Jejak yang <em class="aksen-miring block">tersimpan di batu</em>
            </h2>
            <a href="{{ Navigasi::url('koleksi.index') }}" class="tautan hidden justify-self-end lg:inline-flex lg:mb-3">Seluruh katalog</a>
        </div>

        @if ($koleksiUnggulan->isEmpty())
            <x-keadaan-kosong judul="Koleksi belum tersedia" ikon="gambar" class="mt-12">
                Koleksi unggulan akan tampil setelah pengelola menambahkan data koleksi.
            </x-keadaan-kosong>
        @else
            {{-- Ponsel: daftar bisa digeser ke samping. Desktop: grid bertingkat 4 kolom. --}}
            <div x-data x-geser class="-mx-5 mt-8 flex snap-x snap-mandatory scroll-px-5 gap-4 overflow-x-auto px-5 pb-2 md:-mx-10 md:scroll-px-10 md:px-10 lg:mx-0 lg:mt-12 lg:grid lg:grid-cols-4 lg:gap-6 lg:overflow-visible lg:px-0 lg:pb-20">
                @foreach ($koleksiUnggulan as $koleksi)
                    <div class="w-[270px] shrink-0 snap-start lg:w-auto {{ $loop->even ? 'lg:translate-y-20' : '' }}">
                        <div x-data x-muncul="{{ $loop->index * 100 }}">
                            @include('publik.beranda._kartu-koleksi', ['koleksi' => $koleksi])
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="mt-4 text-kecil text-cokelat-600 lg:hidden">Geser ke samping untuk koleksi lain.</p>
        @endif

        <a href="{{ Navigasi::url('koleksi.index') }}" class="tombol tombol-garis mt-6 w-full lg:mt-16">Buka seluruh katalog koleksi</a>
    </section>

    {{-- ================= 5. Artikel terbaru ================= --}}
    <section class="wadah grid gap-8 pt-24 lg:grid-cols-[360px_minmax(0,1fr)] lg:gap-16 lg:pt-40">
        <div class="flex flex-col gap-6 lg:sticky lg:top-28 lg:self-start" x-data x-muncul>
            <x-label-seksi nomor="03" label="Artikel terbaru" />
            <h2 class="teks-display-l uppercase">Cerita untuk <em class="aksen-miring block">dibaca</em></h2>
            <p class="max-w-[320px] text-isi text-cokelat-600">Tulisan dari pengelola untuk dibaca sebelum atau sesudah berkunjung.</p>
            <a href="{{ route('artikel.index') }}" class="tautan self-start">Semua artikel</a>
        </div>

        @if ($artikelTerbaru->isEmpty())
            <x-keadaan-kosong judul="Belum ada artikel terbit" ikon="daftar" />
        @else
            @php $utama = $artikelTerbaru->first(); @endphp
            <div class="flex flex-col">
                <a href="{{ route('artikel.show', $utama) }}" class="group flex flex-col gap-4 pb-10" x-data x-muncul>
                    <div class="h-[210px] overflow-hidden rounded-kartu bg-foto sm:h-[320px] lg:h-[400px]">
                        @if ($utama->gambar_url)
                            <img src="{{ $utama->gambar_url }}" alt="Foto sampul {{ $utama->title }}" loading="lazy"
                                 class="size-full object-cover transition duration-500 group-hover:scale-[1.03]">
                        @else
                            <x-tempat-foto nada="lembut" class="size-full rounded-none" keterangan="Foto sampul artikel" />
                        @endif
                    </div>
                    <p class="flex items-center gap-2 text-kecil text-cokelat-600">
                        01 · {{ $utama->published_at->translatedFormat('j F Y') }}
                        <x-penanda-contoh />
                    </p>
                    <h3 class="teks-h1 transition-colors group-hover:text-maroon">{{ $utama->title }}</h3>
                    <p class="max-w-[640px] text-isi text-cokelat-600">{{ $utama->ringkasan() }}</p>
                    <span class="tautan self-start">Baca artikel</span>
                </a>

                <div class="border-t border-cokelat">
                    @foreach ($artikelTerbaru->skip(1) as $artikel)
                        <x-baris-artikel :artikel="$artikel" :nomor="$loop->iteration + 1" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ================= 6. Reservasi ================= --}}
    <section class="wadah pt-24 lg:pt-32">
        <div class="relative overflow-hidden rounded-banner bg-maroon px-6 py-10 text-ivory sm:px-12 lg:px-20 lg:py-20" x-data x-muncul>
            {{-- Medali kawung yang berputar pelan sebagai ornamen --}}
            <div class="pointer-events-none absolute -right-20 -top-28 size-[260px] lg:-right-[120px] lg:-top-[120px] lg:size-[460px]" aria-hidden="true">
                <div class="size-full animate-[spin_90s_linear_infinite] rounded-full border border-emas bg-kawung bg-[length:50px_50px] opacity-40"></div>
                <div class="absolute inset-[13%] rounded-full border border-emas/70"></div>
            </div>

            <div class="relative flex items-center gap-3">
                <span class="font-display text-angka-sedang text-emas">04</span>
                <span class="size-[7px] rotate-45 bg-emas"></span>
                <span class="text-label font-semibold">Reservasi kunjungan</span>
            </div>

            <h2 class="relative mt-8 max-w-[980px] font-display text-h1-ponsel font-medium uppercase lg:mt-10 lg:text-display-l">
                Rencanakan kunjungan Anda, lalu <em class="text-emas">pantau keputusannya</em> dari akun.
            </h2>

            @php
                $alur = [
                    ['Pilih jadwal', 'Tanggal, jam, dan sisa kapasitas slot terlihat sebelum Anda mengisi formulir.', []],
                    ['Isi formulir', 'Nama penanggung jawab, nomor telepon, jumlah pengunjung, dan catatan bila perlu.', []],
                    ['Ditinjau pengelola', 'Pengajuan tersimpan dengan status:', ['pending']],
                    ['Lihat keputusan', 'Hasilnya muncul di Riwayat Reservasi:', ['approved', 'rejected']],
                ];
            @endphp
            <ol class="relative mt-10 grid sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-4">
                @foreach ($alur as $i => [$judul, $isi, $status])
                    <li class="flex gap-4 border-t border-emas/70 py-4 sm:flex-col sm:gap-2.5 sm:py-6">
                        <span class="font-display text-angka-sedang text-emas sm:text-angka">0{{ $i + 1 }}</span>
                        <div class="flex flex-col gap-2">
                            <p class="text-label font-semibold">{{ $judul }}</p>
                            <p class="text-kecil text-di-gelap">{{ $isi }}</p>
                            @if ($status)
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach ($status as $s)
                                        <x-badge-status :status="$s" />
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="relative mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
                <a href="{{ Navigasi::url('reservasi.index') }}" class="tombol tombol-terang">Ajukan jadwal kunjungan</a>
                <p class="text-kecil text-di-gelap">Perlu masuk akun. Tidak ada pembayaran di situs ini.</p>
            </div>
        </div>
    </section>

    {{-- ================= 7. Kuis ================= --}}
    <section class="wadah grid gap-12 pb-24 pt-24 lg:grid-cols-[500px_minmax(0,1fr)] lg:gap-24 lg:pb-32 lg:pt-32">
        <div class="flex flex-col gap-6" x-data x-muncul>
            <x-label-seksi nomor="05" label="Kuis edukasi" />
            <h2 class="teks-display-l uppercase">Uji <em class="aksen-miring">pemahaman</em> Anda</h2>
            <p class="teks-lead text-cokelat-600">Kuis singkat tentang sejarah dan koleksi museum. Skor tersimpan di akun setelah kuis selesai.</p>

            @if ($daftarKuis->isNotEmpty())
                <ul class="flex flex-col">
                    @foreach ($daftarKuis as $kuis)
                        <li class="flex items-center justify-between gap-3 border-t border-garis py-3.5 last:border-b">
                            <span class="text-isi">{{ $kuis->title }}</span>
                            <x-penanda-contoh />
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-5">
                <a href="{{ Navigasi::url('kuis.index') }}" class="tombol tombol-utama self-start">Kerjakan kuis</a>
                @guest
                    <p class="flex items-center gap-1.5 text-kecil text-cokelat-600">
                        <x-ikon nama="kunci" class="size-4" />
                        Masuk untuk mengerjakan
                    </p>
                @endguest
            </div>
        </div>

        {{-- Pratinjau soal yang bisa dicoba. Hanya tampilan, tidak dinilai dan tanpa kunci jawaban. --}}
        @if ($kuisPratinjau && $kuisPratinjau->questions->isNotEmpty())
            @php
                $soalPratinjau = $kuisPratinjau->questions->map(fn ($soal) => [
                    'teks' => $soal->question_text,
                    'opsi' => $soal->options->pluck('option_text')->values(),
                ])->values();
            @endphp
            <div class="relative lg:pl-16" x-data="{ soal: @js($soalPratinjau), ke: 0, pilih: null }" x-muncul="150">
                <div class="absolute bottom-[-12px] left-0 top-10 hidden w-[92%] -rotate-6 overflow-hidden rounded-kartu bg-cokelat lg:block" aria-hidden="true">
                    <div class="size-full bg-kawung bg-[length:40px_40px] opacity-[0.16]"></div>
                </div>

                <div class="relative flex flex-col gap-5 rounded-kartu border border-garis bg-putih p-5 shadow-dialog sm:p-8">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-kecil text-cokelat-600">
                            {{ $kuisPratinjau->title }} · soal <span x-text="ke + 1"></span> dari {{ $kuisPratinjau->questions_count }}
                        </p>
                        <x-penanda-contoh teks="pratinjau" />
                    </div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-lembut">
                        <div class="h-full rounded-full bg-maroon transition-all duration-500"
                             :style="`width: ${((ke + 1) / {{ $kuisPratinjau->questions_count }}) * 100}%`"></div>
                    </div>
                    <p class="teks-h3" x-text="soal[ke].teks"></p>

                    <div class="flex flex-col gap-3" role="radiogroup" aria-label="Pilihan jawaban pratinjau">
                        <template x-for="(opsi, i) in soal[ke].opsi" :key="ke + '-' + i">
                            <button type="button" role="radio" :aria-checked="pilih === i"
                                    x-on:click="pilih = i"
                                    class="flex min-h-14 items-center gap-3.5 rounded-input border px-4 py-2 text-left text-isi transition"
                                    :class="pilih === i ? 'border-maroon bg-maroon-lembut' : 'border-garis-kuat bg-putih hover:border-cokelat'">
                                <span class="flex size-[30px] shrink-0 items-center justify-center rounded-full border text-label font-semibold transition"
                                      :class="pilih === i ? 'border-maroon bg-maroon text-putih' : 'border-garis-kuat'"
                                      x-text="'ABCD'[i]"></span>
                                <span x-text="opsi"></span>
                            </button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" class="tombol tombol-garis tombol-kecil" :disabled="ke === 0" x-on:click="ke--; pilih = null">Sebelumnya</button>
                        <button type="button" class="tombol tombol-garis tombol-kecil" :disabled="ke === soal.length - 1" x-on:click="ke++; pilih = null">Berikutnya</button>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

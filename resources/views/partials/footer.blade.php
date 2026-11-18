@php
    use App\Support\Navigasi;

    // $profilMuseum dikirim oleh view composer di AppServiceProvider
    $kontak = [
        ['ikon' => 'lokasi', 'teks' => $profilMuseum?->address ?? 'Alamat lengkap museum'],
        ['ikon' => 'jam', 'teks' => $profilMuseum?->opening_hours ?? 'Jam buka'],
        ['ikon' => 'surat', 'teks' => $profilMuseum?->email ?? 'Kontak resmi museum'],
    ];
@endphp

<footer class="mt-auto overflow-hidden bg-cokelat text-di-gelap">
    <div class="h-2.5 w-full bg-ivory bg-tumpal bg-repeat-x bg-[length:auto_10px]" aria-hidden="true"></div>

    <div class="wadah flex flex-col gap-10 pb-8 pt-16 lg:gap-14 lg:pt-[88px]">
        <p class="font-display text-[clamp(44px,8.9vw,128px)] font-medium uppercase leading-[0.85] tracking-[-0.01em] text-ivory" x-data x-muncul>
            Swaraning <span class="italic text-emas">Purwa</span>
        </p>

        <div class="grid gap-10 md:grid-cols-2 lg:flex lg:gap-16">
            <p class="max-w-[360px] text-kecil">
                Media informasi dan edukasi digital untuk mengenal koleksi, sejarah, dan warisan budaya di Museum Mpu Purwa Kota Malang.
            </p>

            <div class="flex gap-16">
                <div class="flex flex-col gap-2.5">
                    <p class="text-label font-semibold text-emas">Jelajahi</p>
                    <a href="{{ Navigasi::url('beranda') }}" class="text-kecil text-ivory transition hover:text-emas">Beranda</a>
                    <a href="{{ Navigasi::url('profil-museum') }}" class="text-kecil text-ivory transition hover:text-emas">Profil Museum</a>
                    <a href="{{ Navigasi::url('koleksi.index') }}" class="text-kecil text-ivory transition hover:text-emas">Katalog Koleksi</a>
                    <a href="{{ Navigasi::url('artikel.index') }}" class="text-kecil text-ivory transition hover:text-emas">Artikel Edukasi</a>
                </div>
                <div class="flex flex-col gap-2.5">
                    <p class="text-label font-semibold text-emas">Akun</p>
                    <a href="{{ Navigasi::url('reservasi.index') }}" class="text-kecil text-ivory transition hover:text-emas">Reservasi Kunjungan</a>
                    <a href="{{ Navigasi::url('kuis.index') }}" class="text-kecil text-ivory transition hover:text-emas">Kuis Edukasi</a>
                    @guest
                        <a href="{{ route('login') }}" class="text-kecil text-ivory transition hover:text-emas">Masuk</a>
                        <a href="{{ route('register') }}" class="text-kecil text-ivory transition hover:text-emas">Daftar</a>
                    @endguest
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <p class="text-label font-semibold text-emas">Kunjungi</p>
                @foreach ($kontak as $baris)
                    <div class="flex flex-wrap items-center gap-2.5">
                        <x-ikon :nama="$baris['ikon']" class="size-[18px] text-emas" />
                        <span class="text-kecil">{{ $baris['teks'] }}</span>
                        <x-penanda-contoh latar="gelap" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-2 border-t border-emas pt-6 text-kecil md:flex-row md:justify-between">
            <p>© {{ now()->year }} Swaraning Purwa. Prototipe akademik mata kuliah Pengembangan Aplikasi Web Lanjut, FILKOM UB.</p>
            <p>Bukan situs resmi Museum Mpu Purwa</p>
        </div>
    </div>
</footer>

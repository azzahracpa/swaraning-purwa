{{--
    Dialog konfirmasi global (cukup dipasang sekali di layout).
    Dibuka dari form dengan $store.konfirmasi.buka($el, {...}), lihat resources/js/interaksi/konfirmasi.js
    Tombol Batal selalu di kiri, tombol kanan menyebut tindakannya.
--}}
<div x-data
     x-show="$store.konfirmasi.terbuka"
     x-on:keydown.escape.window="$store.konfirmasi.tutup()"
     class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center"
     role="dialog" aria-modal="true" aria-labelledby="judul-konfirmasi"
     x-cloak>
    <div x-show="$store.konfirmasi.terbuka"
         x-transition.opacity.duration.200ms
         x-on:click="$store.konfirmasi.tutup()"
         class="absolute inset-0 bg-cokelat/40 backdrop-blur-[2px]"></div>

    <div x-show="$store.konfirmasi.terbuka"
         x-trap.noscroll="$store.konfirmasi.terbuka"
         x-transition:enter="transition duration-200 ease-out"
         x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-2 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition duration-150 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="relative flex w-full max-w-[480px] flex-col gap-5 rounded-kartu bg-putih p-6 shadow-dialog sm:p-8">
        <div class="flex items-center gap-3.5">
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full"
                  :class="$store.konfirmasi.jenis === 'setujui' ? 'bg-status-disetujui-latar text-status-disetujui-teks' : 'bg-status-ditolak-latar text-status-ditolak-teks'">
                <x-ikon nama="centang" x-show="$store.konfirmasi.jenis === 'setujui'" />
                <x-ikon nama="peringatan" x-show="$store.konfirmasi.jenis !== 'setujui'" />
            </span>
            <h2 id="judul-konfirmasi" class="teks-h3 flex-1" x-text="$store.konfirmasi.judul"></h2>
        </div>

        <p class="text-isi text-cokelat-600" x-show="$store.konfirmasi.isi" x-text="$store.konfirmasi.isi"></p>

        <div class="rounded-lg bg-lembut px-4 py-3.5 text-kecil text-cokelat"
             x-show="$store.konfirmasi.ringkasan" x-text="$store.konfirmasi.ringkasan"></div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button" class="tombol tombol-garis" x-on:click="$store.konfirmasi.tutup()">Batal</button>
            <button type="button" class="tombol"
                    :class="$store.konfirmasi.jenis === 'setujui' ? 'tombol-utama' : 'tombol-bahaya'"
                    x-on:click="$store.konfirmasi.setuju()"
                    x-text="$store.konfirmasi.tombol"></button>
        </div>
    </div>
</div>

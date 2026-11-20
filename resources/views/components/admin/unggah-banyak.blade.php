@props(['name' => 'foto', 'maks' => 8, 'sisa' => 8])

{{--
    Unggah banyak foto sekaligus dengan pratinjau. Bisa seret beberapa berkas ke kotak.
    Berkas yang bukan gambar atau lebih dari 2 MB langsung ditolak di browser;
    validasi utama tetap di Form Request.
--}}
<div class="flex flex-col gap-3"
     x-data="{
        daftar: [],
        galat: [],
        seret: false,
        sisa: {{ (int) $sisa }},
        tambah(berkasList) {
            this.galat = [];
            const dt = new DataTransfer();
            this.daftar.forEach(d => dt.items.add(d.berkas));
            for (const berkas of berkasList) {
                if (dt.files.length >= this.sisa) { this.galat.push('Batas ' + {{ (int) $maks }} + ' foto per koleksi tercapai.'); break; }
                if (! ['image/jpeg', 'image/png', 'image/webp'].includes(berkas.type)) { this.galat.push(berkas.name + ' bukan gambar JPG, PNG, atau WEBP.'); continue; }
                if (berkas.size > 2 * 1024 * 1024) { this.galat.push(berkas.name + ' berukuran ' + (berkas.size / 1048576).toFixed(1) + ' MB, melebihi batas 2 MB.'); continue; }
                dt.items.add(berkas);
                this.daftar.push({ berkas, url: URL.createObjectURL(berkas) });
            }
            this.$refs.berkas.files = dt.files;
        },
        buang(i) {
            this.daftar.splice(i, 1);
            const dt = new DataTransfer();
            this.daftar.forEach(d => dt.items.add(d.berkas));
            this.$refs.berkas.files = dt.files;
        },
     }">
    <label class="flex cursor-pointer flex-col items-center gap-2 rounded-kartu border border-dashed p-6 text-center transition"
           :class="seret ? 'border-maroon bg-maroon-lembut' : 'border-garis-kuat bg-lembut hover:border-cokelat'"
           x-on:dragover.prevent="seret = true" x-on:dragleave.prevent="seret = false"
           x-on:drop.prevent="seret = false; tambah($event.dataTransfer.files)">
        <x-ikon nama="gambar" class="size-6 text-emas-700" />
        <span class="text-label font-semibold">Seret foto ke sini, atau pilih berkas</span>
        <span class="text-kecil text-cokelat-600">JPG, PNG, atau WEBP, maksimal 2 MB per foto · sisa {{ $sisa }} dari {{ $maks }}</span>
        <input type="file" name="{{ $name }}[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" x-ref="berkas"
               x-on:change="tambah([...$event.target.files])">
    </label>

    <div class="grid grid-cols-3 gap-2" x-show="daftar.length" x-cloak>
        <template x-for="(d, i) in daftar" :key="d.url">
            <div class="group relative aspect-square overflow-hidden rounded-input bg-foto">
                <img :src="d.url" alt="" class="size-full object-cover">
                <button type="button" x-on:click="buang(i)" class="absolute right-1 top-1 flex size-7 items-center justify-center rounded-full bg-cokelat/80 text-ivory opacity-0 transition group-hover:opacity-100 focus:opacity-100" aria-label="Batalkan foto ini">
                    <x-ikon nama="tutup" class="size-3.5" />
                </button>
            </div>
        </template>
    </div>

    <template x-for="pesan in galat" :key="pesan">
        <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> <span x-text="pesan"></span></p>
    </template>
    @error($name)
        <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
    @enderror
    @error($name.'.*')
        <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
    @enderror
</div>

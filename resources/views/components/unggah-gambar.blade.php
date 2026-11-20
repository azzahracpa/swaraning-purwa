@props([
    'name' => 'image',
    'label' => 'Gambar',
    'urlSekarang' => null,
    'bantuan' => 'Opsional. JPG, PNG, atau WEBP, maksimal 2 MB.',
    'bolehHapus' => true,
    'kelasPratinjau' => 'aspect-[16/10]',
])

{{--
    Unggah satu gambar dengan pratinjau. Bisa klik "Pilih gambar" atau seret berkas ke kotak.
    Ukuran dicek dulu di browser (2 MB), tetapi validasi utama tetap di Form Request (server).
    Mengirim field {{ $name }} (berkas) dan hapus_{{ $name }} = 1 kalau gambar lama dihapus.
--}}
@php $kunciGalat = $name; @endphp

<div class="flex flex-col gap-3"
     x-data="{
        pratinjau: @js($urlSekarang),
        namaBerkas: '',
        galat: '',
        hapus: false,
        seret: false,
        pilih(berkas) {
            this.galat = '';
            if (! berkas) return;
            if (! ['image/jpeg', 'image/png', 'image/webp'].includes(berkas.type)) {
                this.galat = berkas.name + ' bukan gambar JPG, PNG, atau WEBP.';
                this.$refs.berkas.value = '';
                return;
            }
            if (berkas.size > 2 * 1024 * 1024) {
                this.galat = berkas.name + ' berukuran ' + (berkas.size / 1048576).toFixed(1) + ' MB, melebihi batas 2 MB.';
                this.$refs.berkas.value = '';
                return;
            }
            this.namaBerkas = berkas.name;
            this.pratinjau = URL.createObjectURL(berkas);
            this.hapus = false;
        },
        jatuhkan(e) {
            this.seret = false;
            const berkas = e.dataTransfer.files[0];
            if (! berkas) return;
            const dt = new DataTransfer();
            dt.items.add(berkas);
            this.$refs.berkas.files = dt.files;
            this.pilih(berkas);
        },
     }">
    <p class="kolom-label">{{ $label }}</p>

    <div class="relative overflow-hidden rounded-kartu border border-dashed transition {{ $kelasPratinjau }}"
         :class="seret ? 'border-maroon bg-maroon-lembut' : 'border-garis-kuat bg-lembut'"
         x-on:dragover.prevent="seret = true" x-on:dragleave.prevent="seret = false" x-on:drop.prevent="jatuhkan($event)">
        <template x-if="pratinjau && ! hapus">
            <img :src="pratinjau" alt="Pratinjau gambar" class="absolute inset-0 size-full object-cover">
        </template>
        <div x-show="! pratinjau || hapus" class="absolute inset-0 flex flex-col items-center justify-center gap-2 p-4 text-center">
            <x-ikon nama="unggah" class="size-6 text-emas-700" />
            <p class="text-label font-semibold">Seret gambar ke sini, atau pilih berkas</p>
            <p class="text-kecil text-cokelat-600">JPG, PNG, atau WEBP, maksimal 2 MB</p>
        </div>
    </div>

    <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp" class="sr-only" x-ref="berkas"
           x-on:change="pilih($event.target.files[0])" id="berkas-{{ $name }}">
    <input type="hidden" name="hapus_{{ $name }}" :value="hapus ? 1 : 0">

    <div class="flex flex-wrap items-center gap-2">
        <label for="berkas-{{ $name }}" class="tombol tombol-garis tombol-kecil cursor-pointer">
            <span x-text="pratinjau && ! hapus ? 'Ganti gambar' : 'Pilih gambar'"></span>
        </label>
        @if ($bolehHapus)
            <button type="button" class="tombol tombol-garis tombol-kecil" x-show="pratinjau && ! hapus"
                    x-on:click="hapus = true; $refs.berkas.value = ''; namaBerkas = ''">Hapus</button>
            <button type="button" class="tautan" x-show="hapus" x-cloak x-on:click="hapus = false">Batalkan hapus</button>
        @endif
        <span class="text-kecil text-cokelat-600" x-text="namaBerkas"></span>
    </div>

    <p class="kolom-pesan-galat" x-show="galat" x-cloak><x-ikon nama="peringatan" class="size-4" /> <span x-text="galat"></span></p>
    @error($kunciGalat)
        <p class="kolom-pesan-galat"><x-ikon nama="peringatan" class="size-4" /> {{ $message }}</p>
    @else
        <p class="kolom-bantuan">{{ $bantuan }}</p>
    @enderror
</div>

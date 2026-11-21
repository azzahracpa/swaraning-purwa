{{-- Isian informasi koleksi, dipakai halaman tambah dan ubah --}}
<x-admin.kotak nomor="01" label="Informasi koleksi">
    <x-kolom-isian name="name" label="Nama koleksi" :value="$koleksi->name" wajib maxlength="255" />
    <x-kolom-isian name="category_id" label="Kategori" type="select" wajib
                   bantuan="Pilih dari daftar kategori. Kategori baru dibuat di menu Kategori.">
        <option value="">Pilih kategori</option>
        @foreach ($kategori as $item)
            <option value="{{ $item->id }}" @selected((int) old('category_id', $koleksi->category_id) === $item->id)>{{ $item->name }}</option>
        @endforeach
    </x-kolom-isian>
    <x-kolom-isian name="description" label="Deskripsi" type="textarea" rows="5" :value="$koleksi->description" wajib
                   placeholder="Tulis deskripsi singkat koleksi untuk pengunjung…" />
    <div class="grid gap-5 sm:grid-cols-2">
        <x-kolom-isian name="historical_period" label="Periode sejarah" :value="$koleksi->historical_period" placeholder="Hindu–Buddha" />
        <x-kolom-isian name="origin" label="Asal temuan" :value="$koleksi->origin" placeholder="Asal temuan (opsional)" />
        <x-kolom-isian name="material" label="Bahan" :value="$koleksi->material" placeholder="Batu andesit" />
        <x-kolom-isian name="location" label="Lokasi di museum" :value="$koleksi->location" placeholder="Ruang pamer utama" />
    </div>
</x-admin.kotak>

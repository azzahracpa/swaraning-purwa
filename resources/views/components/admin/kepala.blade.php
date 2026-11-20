@props(['judul', 'aksen' => null, 'deskripsi' => null])

{{--
    Kepala halaman admin: judul besar (kata kedua miring maroon), deskripsi, dan tombol aksi.
    Contoh: <x-admin.kepala judul="Kelola" aksen="koleksi" deskripsi="...">
                <a href="..." class="tombol tombol-utama">Tambah koleksi</a>
            </x-admin.kepala>
--}}
<div {{ $attributes->merge(['class' => 'flex flex-col gap-5 pb-8 lg:flex-row lg:items-end lg:justify-between']) }}>
    <div class="max-w-[640px]">
        <h1 class="teks-display-l uppercase">
            {{ $judul }} @if ($aksen)<span class="aksen-miring">{{ $aksen }}</span>@endif
        </h1>
        @if ($deskripsi)
            <p class="mt-3 text-isi text-cokelat-600">{{ $deskripsi }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-3">{{ $slot }}</div>
    @endif
</div>

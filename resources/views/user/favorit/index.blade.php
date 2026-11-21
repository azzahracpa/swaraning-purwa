@extends('layouts.user')

@section('title', 'Koleksi Favorit')

@section('kepala')
    <x-breadcrumb :items="['Beranda' => route('beranda'), 'Akun saya' => route('dashboard'), 'Koleksi Favorit' => null]" />
    <h1 class="teks-display-l mt-6 uppercase" x-data x-muncul>Koleksi <span class="aksen-miring">favorit</span></h1>
    <p class="mt-4 max-w-[560px] text-isi text-cokelat-600">
        Koleksi yang Anda simpan dari katalog. Buka kartu untuk melihat detail, atau hapus bila tidak diperlukan lagi.
    </p>
@endsection

@section('content')
    @if ($favorit->isEmpty() && ! $kategoriAktif)
        <x-keadaan-kosong judul="Belum ada koleksi favorit" ikon="hati">
            Tekan “Simpan ke favorit” di halaman detail koleksi untuk menyimpannya di sini.
            <x-slot:aksi><a href="{{ route('koleksi.index') }}" class="tombol tombol-utama tombol-kecil">Buka katalog koleksi</a></x-slot:aksi>
        </x-keadaan-kosong>
    @else
        <form method="GET" action="{{ route('favorit.index') }}" class="flex flex-col gap-3 border-b border-garis pb-6 lg:flex-row lg:items-center">
            @if ($kategoriAktif)
                <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
            @endif
            <div class="flex flex-wrap items-center gap-2">
                <x-admin.chip :href="route('favorit.index', array_filter(['urut' => request('urut')]))" :aktif="! $kategoriAktif">Semua</x-admin.chip>
                @foreach ($kategori as $item)
                    <x-admin.chip :href="route('favorit.index', array_filter(['kategori' => $item->id, 'urut' => request('urut')]))"
                                  :aktif="$kategoriAktif === $item->id">{{ $item->name }}</x-admin.chip>
                @endforeach
                <span class="ml-1 flex items-center gap-2 text-kecil text-cokelat-600">{{ $favorit->count() }} koleksi tersimpan <x-penanda-contoh /></span>
            </div>
            <label class="relative lg:ml-auto">
                <span class="sr-only">Urutkan</span>
                <x-ikon nama="urut" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-cokelat-600" />
                <select name="urut" class="kolom-input h-10 pl-9 text-kecil" x-data x-on:change="$el.form.requestSubmit()">
                    <option value="">Urutkan: terakhir disimpan</option>
                    <option value="nama" @selected(request('urut') === 'nama')>Urutkan: nama A–Z</option>
                </select>
            </label>
        </form>

        <div class="mt-8 grid gap-x-6 gap-y-12 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($favorit as $item)
                <div class="flex flex-col gap-4" x-data x-muncul="{{ ($loop->index % 3) * 90 }}">
                    <x-kartu-koleksi :koleksi="$item->collection" />
                    <div class="flex items-center justify-between border-t border-garis px-1 pt-3 text-kecil">
                        <span class="text-cokelat-600">Disimpan {{ $item->created_at->translatedFormat('j M Y') }}</span>
                        <form method="POST" action="{{ route('favorit.destroy', $item->collection) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="tautan" aria-label="Hapus {{ $item->collection->name }} dari favorit">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-kecil text-cokelat-600 sm:col-span-2 xl:col-span-3">Tidak ada favorit di kategori ini.</p>
            @endforelse
        </div>
    @endif

    {{-- Toast "dihapus dari favorit" dengan tombol Urungkan --}}
    @if (session('favorit_dihapus'))
        @php $dihapus = session('favorit_dihapus'); @endphp
        <div x-data="{ tampil: true }" x-show="tampil" x-init="setTimeout(() => tampil = false, 8000)"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-y-4 opacity-0"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="translate-y-4 opacity-0"
             class="fixed bottom-6 left-1/2 z-40 flex w-[calc(100%-40px)] max-w-md -translate-x-1/2 items-center gap-4 rounded-kartu bg-cokelat px-5 py-3 text-kecil text-ivory shadow-dialog sm:left-auto sm:right-6 sm:translate-x-0"
             role="status">
            <p class="flex-1">{{ $dihapus['nama'] }} dihapus dari favorit.</p>
            <form method="POST" action="{{ route('favorit.store') }}">
                @csrf
                <input type="hidden" name="collection_id" value="{{ $dihapus['id'] }}">
                <button type="submit" class="tombol tombol-kecil h-9 border border-emas px-3 text-emas hover:bg-emas hover:text-cokelat">Urungkan</button>
            </form>
        </div>
    @endif
@endsection

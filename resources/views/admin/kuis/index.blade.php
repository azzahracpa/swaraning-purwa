@extends('layouts.admin')

@section('title', 'Kelola Kuis')
@section('halaman', 'Kelola Kuis')

@section('content')
    <x-admin.kepala judul="Kelola" aksen="kuis"
                    deskripsi="Hanya kuis berstatus Aktif yang tampil di halaman Kuis Edukasi. Kuis bisa diaktifkan setelah semua soalnya lengkap.">
        <a href="{{ route('admin.kuis.create') }}" class="tombol tombol-utama">Buat kuis</a>
    </x-admin.kepala>

    <p class="mb-4 flex items-center gap-3 text-label font-semibold">{{ $kuis->count() }} kuis <x-penanda-contoh teks="data contoh" /></p>

    @if ($kuis->isEmpty())
        <x-keadaan-kosong judul="Belum ada kuis" ikon="bantuan">
            <x-slot:aksi><a href="{{ route('admin.kuis.create') }}" class="tombol tombol-utama tombol-kecil">Buat kuis</a></x-slot:aksi>
        </x-keadaan-kosong>
    @else
        <div class="kartu overflow-x-auto">
            <table class="tabel min-w-[900px]">
                <thead class="bg-lembut">
                    <tr><th>Kuis</th><th>Soal</th><th>Total poin</th><th>Dikerjakan</th><th>Status</th><th>Tampilkan</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody>
                    @foreach ($kuis as $item)
                        @php $lengkap = $item->bisaDiaktifkan(); @endphp
                        <tr>
                            <td>
                                <p class="font-semibold">{{ $item->title }}</p>
                                <p class="max-w-[320px] text-cokelat-600">{{ $item->description }}</p>
                            </td>
                            <td class="whitespace-nowrap">{{ $item->questions_count }} soal</td>
                            <td class="whitespace-nowrap">{{ (int) $item->questions_sum_points }} poin</td>
                            <td class="whitespace-nowrap">{{ $item->attempts_count ? $item->attempts_count.' kali' : 'Belum ada' }}</td>
                            <td>
                                @if ($item->is_active)
                                    <x-badge-keadaan nada="positif" class="gap-1.5"><x-ikon nama="centang" class="size-3.5" />Aktif</x-badge-keadaan>
                                @elseif (! $lengkap)
                                    <x-badge-keadaan nada="negatif" class="gap-1.5"><x-ikon nama="peringatan" class="size-3.5" />Belum lengkap</x-badge-keadaan>
                                @else
                                    <x-badge-keadaan nada="netral" class="gap-1.5"><x-ikon nama="larangan" class="size-3.5" />Nonaktif</x-badge-keadaan>
                                @endif
                            </td>
                            <td>
                                {{-- Sakelar aktif: dikirim langsung saat diklik --}}
                                <form method="POST" action="{{ route('admin.kuis.status', $item) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" @disabled(! $item->is_active && ! $lengkap)
                                            class="group flex items-center gap-3 disabled:cursor-not-allowed"
                                            aria-label="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $item->title }}">
                                        <span class="relative h-6 w-11 rounded-full transition-colors {{ $item->is_active ? 'bg-maroon' : 'bg-garis-kuat group-disabled:opacity-50' }}">
                                            <span class="absolute left-0.5 top-0.5 size-5 rounded-full bg-putih shadow transition-transform {{ $item->is_active ? 'translate-x-5' : '' }}"></span>
                                        </span>
                                        <span class="text-label font-semibold">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                                @if (! $item->is_active && ! $lengkap)
                                    <p class="mt-1 text-kecil text-status-ditolak-teks">Lengkapi soal dulu</p>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.kuis.edit', $item) }}" class="tombol tombol-garis tombol-kecil">{{ $lengkap ? 'Ubah' : 'Perbaiki' }}</a>
                                    @if (! $item->attempts_count)
                                        <form method="POST" action="{{ route('admin.kuis.destroy', $item) }}" x-data
                                              x-on:submit.prevent="$store.konfirmasi.buka($el, {
                                                  judul: 'Hapus kuis ini?',
                                                  isi: 'Semua soal dan pilihan jawabannya ikut terhapus.',
                                                  ringkasan: @js($item->title),
                                                  tombol: 'Hapus permanen',
                                              })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="tombol-ikon size-10 text-status-ditolak-teks" aria-label="Hapus {{ $item->title }}">
                                                <x-ikon nama="tutup" class="size-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <p class="mt-4 flex gap-2 text-kecil text-cokelat-600">
        <x-ikon nama="info" class="size-4 shrink-0" />
        Syarat aktif: setiap soal punya sedikitnya dua pilihan jawaban dan tepat satu jawaban benar. Kuis yang sudah dikerjakan pengguna tidak dapat dihapus, hanya dinonaktifkan.
    </p>
@endsection

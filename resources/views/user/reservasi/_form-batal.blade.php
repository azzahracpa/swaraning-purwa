{{-- Tombol batalkan pengajuan (hanya untuk status Menunggu), memakai dialog konfirmasi --}}
<form method="POST" action="{{ route('reservasi.batal', $item) }}" x-data
      x-on:submit.prevent="$store.konfirmasi.buka($el, {
          judul: 'Batalkan pengajuan ini?',
          isi: 'Pengajuan yang dibatalkan tidak bisa diaktifkan lagi. Anda bisa mengajukan reservasi baru kapan saja.',
          ringkasan: @js($item->booking_code.' · '.$item->schedule->visit_date->translatedFormat('j M Y').' · '.$item->schedule->rentangJam().' · '.$item->total_visitors.' orang'),
          tombol: 'Batalkan pengajuan',
      })">
    @csrf
    @method('PATCH')
    <button type="submit" class="tombol tombol-bahaya {{ $kecil ?? false ? 'tombol-kecil' : '' }}">Batalkan pengajuan</button>
</form>

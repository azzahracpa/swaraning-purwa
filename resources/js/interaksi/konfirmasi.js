/**
 * Store Alpine untuk dialog konfirmasi.
 * Form yang butuh konfirmasi (hapus, setujui, tolak, batal) memanggil:
 *
 *   <form method="POST" action="..." x-data
 *         x-on:submit.prevent="$store.konfirmasi.buka($el, {
 *             judul: 'Hapus koleksi ini?',
 *             isi: 'Data yang dihapus tidak bisa dikembalikan.',
 *             ringkasan: 'Arca Ganesha',
 *             tombol: 'Hapus permanen',
 *             jenis: 'hapus'
 *         })">
 *
 * Form baru benar-benar dikirim setelah pengguna menekan tombol di dialog.
 */
export default {
    terbuka: false,
    judul: '',
    isi: '',
    ringkasan: '',
    tombol: 'Lanjutkan',
    // hapus | tolak | setujui
    jenis: 'hapus',
    form: null,

    buka(form, pilihan = {}) {
        this.form = form;
        this.judul = pilihan.judul ?? 'Lanjutkan tindakan ini?';
        this.isi = pilihan.isi ?? '';
        this.ringkasan = pilihan.ringkasan ?? '';
        this.tombol = pilihan.tombol ?? 'Lanjutkan';
        this.jenis = pilihan.jenis ?? 'hapus';
        this.terbuka = true;
    },

    tutup() {
        this.terbuka = false;
        this.form = null;
    },

    setuju() {
        if (this.form) {
            // submit() bawaan tidak memicu event submit lagi, jadi tidak berulang
            this.form.submit();
        }
        this.terbuka = false;
    },
};

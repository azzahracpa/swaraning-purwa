<?php

/*
|--------------------------------------------------------------------------
| Daftar menu navigasi
|--------------------------------------------------------------------------
|
| Menu disimpan di satu tempat supaya layout tidak perlu diubah setiap ada
| modul baru. "route" adalah nama route, "aktif" adalah pola nama route
| yang membuat menu terlihat aktif. Kalau route belum dibuat oleh anggota
| yang bertugas, link otomatis mengarah ke "#" (lihat App\Support\Navigasi).
|
*/

return [

    // Navbar halaman publik
    'publik' => [
        ['label' => 'Beranda', 'route' => 'beranda', 'aktif' => 'beranda'],
        ['label' => 'Profil Museum', 'route' => 'profil-museum', 'aktif' => 'profil-museum'],
        ['label' => 'Koleksi', 'route' => 'koleksi.index', 'aktif' => 'koleksi.*'],
        ['label' => 'Artikel', 'route' => 'artikel.index', 'aktif' => 'artikel.*'],
        ['label' => 'Reservasi', 'route' => 'reservasi.index', 'aktif' => 'reservasi.*'],
        ['label' => 'Kuis', 'route' => 'kuis.index', 'aktif' => 'kuis.*'],
    ],

    // Sidebar area akun user. "pendek" dipakai untuk chip di layar ponsel.
    'akun' => [
        ['label' => 'Dashboard', 'pendek' => 'Dashboard', 'route' => 'dashboard', 'aktif' => 'dashboard'],
        ['label' => 'Reservasi Kunjungan', 'pendek' => 'Reservasi', 'route' => 'reservasi.index', 'aktif' => ['reservasi.index', 'reservasi.create']],
        ['label' => 'Riwayat Reservasi', 'pendek' => 'Riwayat', 'route' => 'reservasi.riwayat', 'aktif' => ['reservasi.riwayat', 'reservasi.show']],
        ['label' => 'Koleksi Favorit', 'pendek' => 'Favorit', 'route' => 'favorit.index', 'aktif' => 'favorit.*'],
        ['label' => 'Kuis Edukasi', 'pendek' => 'Kuis', 'route' => 'kuis.index', 'aktif' => 'kuis.*'],
        ['label' => 'Edit Profil', 'pendek' => 'Profil', 'route' => 'profile.edit', 'aktif' => 'profile.*'],
    ],

    // Sidebar area pengelola (admin), dikelompokkan sesuai desain
    'admin' => [
        'Ringkasan' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'aktif' => 'admin.dashboard', 'ikon' => 'rumah'],
        ],
        'Kunjungan' => [
            ['label' => 'Reservasi', 'route' => 'admin.reservasi.index', 'aktif' => 'admin.reservasi.*', 'ikon' => 'daftar', 'jumlah' => 'reservasi_pending'],
            ['label' => 'Jadwal Kunjungan', 'route' => 'admin.jadwal.index', 'aktif' => 'admin.jadwal.*', 'ikon' => 'kalender'],
        ],
        'Konten' => [
            ['label' => 'Koleksi', 'route' => 'admin.koleksi.index', 'aktif' => 'admin.koleksi.*', 'ikon' => 'gambar'],
            ['label' => 'Kategori', 'route' => 'admin.kategori.index', 'aktif' => 'admin.kategori.*', 'ikon' => 'menu'],
            ['label' => 'Artikel', 'route' => 'admin.artikel.index', 'aktif' => 'admin.artikel.*', 'ikon' => 'edit'],
            ['label' => 'Profil Museum', 'route' => 'admin.profil-museum.edit', 'aktif' => 'admin.profil-museum.*', 'ikon' => 'lokasi'],
        ],
        'Edukasi' => [
            ['label' => 'Kuis', 'route' => 'admin.kuis.index', 'aktif' => 'admin.kuis.*', 'ikon' => 'bantuan'],
        ],
    ],

];

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| File ini sengaja dibuat pendek. Setiap modul punya file route sendiri di
| folder routes/modules/ supaya anggota tim tidak saling bentrok saat merge.
| Semua file .php di folder itu dimuat otomatis sesuai urutan nama file,
| termasuk route autentikasi (routes/modules/auth.php).
|
*/

$fileModul = glob(__DIR__.'/modules/*.php') ?: [];
sort($fileModul);

foreach ($fileModul as $file) {
    require $file;
}

<?php

namespace App\Http\Requests\Admin;

/**
 * Aturan ubah artikel sama dengan aturan tambah artikel.
 * Dipisah supaya mengikuti pola StoreXRequest dan UpdateXRequest.
 */
class UpdateArtikelRequest extends StoreArtikelRequest {}

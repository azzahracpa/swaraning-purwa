<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriRequest;
use App\Http\Requests\Admin\UpdateKategoriRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
{
    /**
     * Daftar kategori. Form tambah ada di samping; ?ubah={id} membuka form ubah.
     */
    public function index(Request $request): View
    {
        $kategori = Category::withCount('collections')->orderBy('name')->get();
        $sedangDiubah = $request->filled('ubah') ? $kategori->firstWhere('id', (int) $request->query('ubah')) : null;

        return view('admin.kategori.index', compact('kategori', 'sedangDiubah'));
    }

    public function store(StoreKategoriRequest $request): RedirectResponse
    {
        $kategori = Category::create($request->validated());

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori "'.$kategori->name.'" berhasil ditambahkan.');
    }

    public function update(UpdateKategoriRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori "'.$category->name.'" berhasil diperbarui.');
    }

    /**
     * Kategori yang masih dipakai koleksi tidak boleh dihapus
     * (foreign key collections.category_id memakai restrict).
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->collections()->exists()) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori "'.$category->name.'" masih dipakai koleksi. Pindahkan koleksinya ke kategori lain lebih dulu.');
        }

        $category->delete();

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

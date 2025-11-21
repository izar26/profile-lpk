<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaraDaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaraDaftarController extends Controller
{
    public function index()
    {
        // Urutkan berdasarkan kolom 'urutan' secara ascending (1, 2, 3...)
        $langkahs = CaraDaftar::orderBy('urutan', 'asc')->get();
        return view('admin.cara-daftar.index', compact('langkahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'urutan' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('cara_daftar', 'public');
        }

        CaraDaftar::create($data);

        return redirect()->route('admin.cara-daftar.index')->with('success', 'Langkah pendaftaran berhasil ditambahkan.');
    }

    public function edit(CaraDaftar $caraDaftar)
    {
        return response()->json($caraDaftar);
    }

    public function update(Request $request, CaraDaftar $caraDaftar)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'urutan' => 'required|integer',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($caraDaftar->gambar) {
                Storage::disk('public')->delete($caraDaftar->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('cara_daftar', 'public');
        }

        $caraDaftar->update($data);

        return redirect()->route('admin.cara-daftar.index')->with('success', 'Langkah berhasil diperbarui.');
    }

    public function destroy(CaraDaftar $caraDaftar)
    {
        if ($caraDaftar->gambar) {
            Storage::disk('public')->delete($caraDaftar->gambar);
        }
        $caraDaftar->delete();

        return redirect()->route('admin.cara-daftar.index')->with('success', 'Langkah berhasil dihapus.');
    }
}
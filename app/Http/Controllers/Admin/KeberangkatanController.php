<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keberangkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KeberangkatanController extends Controller
{
    public function index()
    {
        // Urutkan dari keberangkatan terbaru
        $keberangkatans = Keberangkatan::latest('tanggal_berangkat')->paginate(10);
        return view('admin.keberangkatan.index', compact('keberangkatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'jumlah_peserta' => 'required|integer|min:1',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('keberangkatan_foto', 'public');
        }

        Keberangkatan::create($data);

        return redirect()->back()->with('success', 'Info keberangkatan berhasil ditambahkan.');
    }

    public function edit(Keberangkatan $keberangkatan)
    {
        return response()->json($keberangkatan);
    }

    public function update(Request $request, Keberangkatan $keberangkatan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'jumlah_peserta' => 'required|integer|min:1',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($keberangkatan->foto) Storage::disk('public')->delete($keberangkatan->foto);
            $data['foto'] = $request->file('foto')->store('keberangkatan_foto', 'public');
        }

        $keberangkatan->update($data);

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Keberangkatan $keberangkatan)
    {
        if ($keberangkatan->foto) Storage::disk('public')->delete($keberangkatan->foto);
        $keberangkatan->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
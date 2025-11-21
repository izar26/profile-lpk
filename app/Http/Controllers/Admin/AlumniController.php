<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index()
    {
        $alumnis = Alumni::latest()->paginate(10);
        return view('admin.alumni.index', compact('alumnis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kerja_dimana' => 'required|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alumni_foto', 'public');
        }

        Alumni::create($data);

        return redirect()->back()->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni)
    {
        return response()->json($alumni);
    }

    public function update(Request $request, Alumni $alumni)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kerja_dimana' => 'required|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'testimoni' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($alumni->foto) Storage::disk('public')->delete($alumni->foto);
            $data['foto'] = $request->file('foto')->store('alumni_foto', 'public');
        }

        $alumni->update($data);

        return redirect()->back()->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function destroy(Alumni $alumni)
    {
        if ($alumni->foto) Storage::disk('public')->delete($alumni->foto);
        $alumni->delete();
        return redirect()->back()->with('success', 'Data alumni dihapus.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefDocumentType;
use Illuminate\Http\Request;

class RefDocumentTypeController extends Controller
{
    public function index()
    {
        $documents = RefDocumentType::latest()->get();
        return view('admin.document_types.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'is_required' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        RefDocumentType::create($request->all());

        return back()->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function update(Request $request, RefDocumentType $documentType)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'is_required' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        $documentType->update($request->all());

        return back()->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy(RefDocumentType $documentType)
    {
        $documentType->delete();
        return back()->with('success', 'Jenis dokumen dihapus.');
    }
}
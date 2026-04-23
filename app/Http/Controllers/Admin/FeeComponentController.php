<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeComponent;
use Illuminate\Http\Request;

class FeeComponentController extends Controller
{
    public function index()
    {
        $components = FeeComponent::orderBy('id', 'asc')->get();
        return view('admin.fee-components.index', compact('components'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        FeeComponent::create($request->only('name', 'amount'));

        return redirect()->route('admin.fee-components.index')
            ->with('success', 'Rincian biaya berhasil ditambahkan.');
    }

    public function update(Request $request, FeeComponent $feeComponent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $feeComponent->update($request->only('name', 'amount'));

        return redirect()->route('admin.fee-components.index')
            ->with('success', 'Rincian biaya berhasil diperbarui.');
    }

    public function destroy(FeeComponent $feeComponent)
    {
        $feeComponent->delete();

        return redirect()->route('admin.fee-components.index')
            ->with('success', 'Rincian biaya berhasil dihapus.');
    }
}

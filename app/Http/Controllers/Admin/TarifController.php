<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarif;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();
        return view('admin.tarif.index', compact('tarifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_kendaraan' => 'required',
            'tarif_per_km' => 'required|integer',
            'tarif_minimum' => 'nullable|integer',
            'biaya_tambahan' => 'nullable|integer',
            'promo' => 'nullable|string',
        ]);

        Tarif::create($request->all());

        return redirect()->back()->with('success', 'Tarif berhasil ditambahkan');
    }

    public function edit(Tarif $tarif)
    {
        return view('admin.tarif.edit', compact('tarif'));
    }

    public function update(Request $request, Tarif $tarif)
    {
        $tarif->update($request->all());
        return redirect()->route('admin.tarif.index')->with('success', 'Tarif berhasil diperbarui');
    }

    public function destroy(Tarif $tarif)
    {
        $tarif->delete();
        return redirect()->back()->with('success', 'Tarif berhasil dihapus');
    }
}
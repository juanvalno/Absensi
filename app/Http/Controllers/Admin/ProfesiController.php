<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profesi;

class ProfesiController extends Controller
{
    public function index()
    {
        $profesis = Profesi::orderBy('name_profesi')->get();
        return view('admin.profesis.index', compact('profesis'));
    }

    public function create()
    {
        return view('admin.profesis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_profesi' => 'required|string|max:255|unique:profesis,name_profesi',
            'tunjangan_profesi' => 'numeric|min:0',
        ]);

        // Clean the currency format
        Profesi::create([
            'name_profesi' => $request->name_profesi,
            'tunjangan_profesi' => $request->tunjangan_profesi
        ]);

        return redirect()->route('profesis.index')
            ->with('success', 'Profesi berhasil ditambahkan');
    }

    public function show(Profesi $profesi)
    {
        return view('admin.profesis.show', compact('profesi'));
    }

    public function edit(Profesi $profesi)
    {
        return view('admin.profesis.edit', compact('profesi'));
    }

    public function update(Request $request, Profesi $profesi)
    {
        $request->validate([
            'name_profesi' => 'required|string|max:255|unique:profesis,name_profesi,'.$profesi->id,
            'tunjangan_profesi' => 'required|numeric|min:0',
        ]);
        $profesi->update([
            'name_profesi' => $request->name_profesi,
            'tunjangan_profesi' => $request->tunjangan_profesi
        ]);

        return redirect()->route('profesis.index')
            ->with('success', 'Profesi berhasil diupdate');
    }

    public function destroy(Profesi $profesi)
    {
        // Check for relationships before deleting
        // For example:
        // if($profesi->karyawans()->exists()) {
        //     return redirect()->route('profesis.index')
        //         ->with('error', 'Hapus semua karyawan dengan profesi ini terlebih dahulu');
        // }

        $profesi->delete();
        return redirect()->route('profesis.index')
            ->with('success', 'Profesi berhasil dihapus');
    }

    private function cleanMoneyFormat($value)
    {
        // Remove any non-numeric characters except for the decimal point
        $cleanValue = preg_replace('/[^0-9.]/', '', $value);

        // Convert to integer (cents)
        return (int) $cleanValue;
    }
}
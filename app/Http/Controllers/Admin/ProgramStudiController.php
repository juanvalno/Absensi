<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgramStudi;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudis = ProgramStudi::orderBy('name_programstudi')->get();
        return view('admin.program_studis.index', compact('programStudis'));
    }

    public function create()
    {
        return view('admin.program_studis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_programstudi' => 'required|string|max:255|unique:program_studis,name_programstudi',
            'education_type' => 'required|in:SMA,non-SMA',
        ]);

        ProgramStudi::create($request->all());

        return redirect()->route('program_studis.index')
            ->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function show(ProgramStudi $programStudi)
    {
        // You can load related models here if needed
        // $programStudi->load('relatedModel');
        return view('admin.program_studis.show', compact('programStudi'));
    }

    public function edit(ProgramStudi $programStudi)
    {
        return view('admin.program_studis.edit', compact('programStudi'));
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'name_programstudi' => 'required|string|max:255|unique:program_studis,name_programstudi,' . $programStudi->id,
            'education_type' => 'required|in:SMA,non-SMA',
        ]);

        $programStudi->update($request->all());

        return redirect()->route('program_studis.index')
            ->with('success', 'Program Studi berhasil diupdate');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        // Check for relationships before deleting
        // For example:
        // if($programStudi->students()->exists()) {
        //     return redirect()->route('program_studis.index')
        //         ->with('error', 'Hapus semua mahasiswa pada program studi ini terlebih dahulu');
        // }

        $programStudi->delete();
        return redirect()->route('program_studis.index')
            ->with('success', 'Program Studi berhasil dihapus');
    }
}

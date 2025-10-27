<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Potongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PotonganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $potongans = Potongan::orderBy('nama_potongan')->get();
        return view('admin.potongans.index', compact('potongans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.potongans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_potongan' => 'required|string|max:255',
            'type' => 'required|in:wajib,tidak_wajib',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Potongan::create($request->all());

        return redirect()->route('potongans.index')
            ->with('success', 'Potongan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Potongan $potongan)
    {
        return view('admin.potongans.show', compact('potongan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Potongan $potongan)
    {
        return view('admin.potongans.edit', compact('potongan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Potongan $potongan)
    {
        $validator = Validator::make($request->all(), [
            'nama_potongan' => 'required|string|max:255',
            'type' => 'required|in:wajib,tidak_wajib',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $potongan->update($request->all());

        return redirect()->route('potongans.index')
            ->with('success', 'Potongan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Potongan $potongan)
    {
        // Check if the potongan is being used in penggajian
        // Uncomment this if you have the relationship set up
        /*
        if ($potongan->penggajian()->count() > 0) {
            return redirect()->route('potongans.index')
                ->with('error', 'Potongan tidak dapat dihapus karena sedang digunakan pada data penggajian');
        }
        */

        $potongan->delete();

        return redirect()->route('potongans.index')
            ->with('success', 'Potongan berhasil dihapus');
    }
}
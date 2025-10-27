<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;

class ShiftController extends Controller
{
    /**
     * Get the next available code for a shift.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextCode(Request $request)
    {
        $prefix = $request->input('prefix', 'SHIFT-');

        // Find the highest code with the given prefix
        $lastShift = \App\Models\Shift::where('kode_shift', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(kode_shift, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $nextNum = 1;

        if ($lastShift) {
            // Extract the numeric part
            $lastCode = $lastShift->kode_shift;
            $numPart = substr($lastCode, strlen($prefix));
            $lastNum = (int)$numPart;
            $nextNum = $lastNum + 1;
        }

        // Format with leading zeros
        $nextCode = str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return response()->json(['nextCode' => $nextCode]);
    }
    public function index()
    {
        $shifts = Shift::orderBy('kode_shift')->get();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_shift' => 'required|string|max:255|unique:shifts,kode_shift',
            'jenis_shift' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
        ]);

        Shift::create($request->all());

        return redirect()->route('shifts.index')
            ->with('success', 'Shift berhasil ditambahkan');
    }

    public function show(Shift $shift)
    {
        return view('admin.shifts.show', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        return view('admin.shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'kode_shift' => 'required|string|max:255|unique:shifts,kode_shift,' . $shift->id,
            'jenis_shift' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
        ]);

        $shift->update($request->all());

        return redirect()->route('shifts.index')
            ->with('success', 'Shift berhasil diupdate');
    }

    public function destroy(Shift $shift)
    {
        // Check if this shift is used in other tables before deleting
        // For example:
        // if($shift->related_models()->exists()) {
        //    return redirect()->route('shifts.index')
        //        ->with('error', 'Cannot delete this shift because it is used in other records');
        // }

        $shift->delete();

        return redirect()->route('shifts.index')
            ->with('success', 'Shift berhasil dihapus');
    }
}

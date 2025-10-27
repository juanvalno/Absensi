<?php

namespace App\Http\Controllers\Admin;

use App\Models\Karyawan;
use App\Models\Uangtunggu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UangTungguController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $karyawanId = $request->get('karyawan_id');
        
        // Build query with eager loading
        $query = Uangtunggu::with('karyawan')->latest();
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('karyawan', function($q) use ($search) {
                    $q->where('nama_karyawan', 'like', "%{$search}%")
                      ->orWhere('nik_karyawan', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }
        
        // Apply date range filter for tanggal_mulai
        if ($startDate) {
            $query->whereDate('tanggal_mulai', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('tanggal_selesai', '<=', $endDate);
        }
        
        // Apply karyawan filter
        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }
        
        // Get counts for summary
        $totalCount = Uangtunggu::count();
        $activeCount = Uangtunggu::where('tanggal_selesai', '>=', now()->format('Y-m-d'))->count();
        $expiredCount = Uangtunggu::where('tanggal_selesai', '<', now()->format('Y-m-d'))->count();
        
        // Paginate results
        $uangTunggus = $query->paginate(15);
        
        // Get karyawan list for dropdown
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        
        return view('admin.uangtunggus.index', compact(
            'uangTunggus', 
            'karyawans',
            'totalCount',
            'activeCount',
            'expiredCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('admin.uangtunggus.create', compact('karyawans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        Uangtunggu::create($request->all());

        return redirect()->route('uangtunggus.index')
            ->with('success', 'Uang Tunggu berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Uangtunggu $uangtunggu)
    {
        $uangtunggu->load('karyawan');
        return view('admin.uangtunggus.show', compact('uangtunggu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Uangtunggu $uangtunggu)
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('admin.uangtunggus.edit', compact('uangtunggu', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Uangtunggu $uangtunggu)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $uangtunggu->update($request->all());

        return redirect()->route('uangtunggus.index')
            ->with('success', 'Uang Tunggu berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Uangtunggu $uangtunggu)
    {
        $uangtunggu->delete();
        return redirect()->route('uangtunggus.index')
            ->with('success', 'Uang Tunggu berhasil dihapus');
    }
}
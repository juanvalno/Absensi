<?php

namespace App\Http\Controllers\Admin;

use App\Models\Keuangan;
use App\Models\PeriodeGaji;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KeuanganExport;

class KeuanganController extends Controller
{
    public function index()
    {
        $keuangans = Keuangan::with(['periode', 'verifikator'])->latest()->get();
        return view('admin.keuangan.index', compact('keuangans'));
    }

    public function show($id)
    {
        $keuangan = Keuangan::with([
            'periode',
            'verifikator',
            'penggajians' => function ($query) {
                $query->with(['karyawan.departemen']);
            }
        ])->findOrFail($id);

        // Check if summary exists and is valid
        if (empty($keuangan->summary)) {
            $summary = [];
            $totalKaryawan = 0;
            $grandTotal = 0;

            foreach ($keuangan->penggajians as $penggajian) {
                $dept = $penggajian->karyawan->departemen->name_departemen ?? 'Tidak Ada';
                $status = $penggajian->karyawan->status_karyawan ?? 'Tidak Ada';

                if (!isset($summary[$dept])) {
                    $summary[$dept] = [
                        'total' => 0,
                        'status' => []
                    ];
                }

                if (!isset($summary[$dept]['status'][$status])) {
                    $summary[$dept]['status'][$status] = [
                        'count' => 0,
                        'total' => 0
                    ];
                }

                $summary[$dept]['status'][$status]['count']++;
                $summary[$dept]['status'][$status]['total'] += $penggajian->gaji_bersih;
                $summary[$dept]['total'] += $penggajian->gaji_bersih;

                $totalKaryawan++;
                $grandTotal += $penggajian->gaji_bersih;
            }

            // Save the calculated summary
            $keuangan->update([
                'summary' => $summary,
                'total_karyawan' => $totalKaryawan,
                'grand_total' => $grandTotal
            ]);
        }

        return view('admin.keuangan.show', compact('keuangan'));
    }

    public function approve(Request $request, $id)
    {
        $keuangan = Keuangan::findOrFail($id);

        if ($keuangan->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:255'
        ]);

        try {
            $keuangan->update([
                'status' => 'disetujui',
                'verifikator_id' => Auth::id(),
                'tanggal_verifikasi' => now(),
                'catatan' => $request->catatan
            ]);

            // Update status verifikasi pada tabel penggajian
            $keuangan->penggajians()->update([
                'status_verifikasi' => 'disetujui'
            ]);

            return redirect()->route('keuangan.index')
                ->with('success', 'Pengajuan gaji berhasil disetujui.');
        } catch (\Exception $e) {
            Log::error('Error approving payroll: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyetujui pengajuan.');
        }
    }

    public function reject(Request $request, $id)
    {
        $keuangan = Keuangan::findOrFail($id);

        if ($keuangan->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'required|string|max:255'
        ]);

        try {
            $keuangan->update([
                'status' => 'ditolak',
                'verifikator_id' => Auth::id(),
                'tanggal_verifikasi' => now(),
                'catatan' => $request->catatan
            ]);

            // Update status verifikasi pada tabel penggajian
            $keuangan->penggajians()->update([
                'status_verifikasi' => 'ditolak'
            ]);

            return redirect()->route('keuangan.index')
                ->with('success', 'Pengajuan gaji berhasil ditolak.');
        } catch (\Exception $e) {
            Log::error('Error rejecting payroll: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menolak pengajuan.');
        }
    }

    public function export($id)
    {
        $keuangan = Keuangan::with(['penggajians.karyawan.departemen', 'periode'])->findOrFail($id);

        // Add debugging to check if the method is being called
        \Log::info('Export method called for keuangan ID: ' . $id);

        // Check if $keuangan has penggajians
        if ($keuangan->penggajians->isEmpty()) {
            return back()->with('error', 'No data available for export');
        }

        // Create a meaningful filename
        $filename = 'Laporan_Gaji_' . $keuangan->periode->nama_periode . '.xlsx';

        // Return the download response
        return (new KeuanganExport($keuangan))
            ->download($filename);
    }
}

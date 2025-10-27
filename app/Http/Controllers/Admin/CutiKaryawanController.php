<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Mastercuti;
use Illuminate\Support\Str;
use App\Models\CutiKaryawan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CutiKaryawanController extends Controller
{
    /**
     * Menampilkan daftar semua pengajuan cuti karyawan
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->get('status');
        $search = $request->get('search');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query with eager loading to reduce N+1 problem
        $query = CutiKaryawan::with(['karyawan', 'masterCuti', 'supervisor', 'approver'])
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($status && $status != 'all') {
            $query->where('status_acc', $status);
        }

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('karyawan', function ($q) use ($search) {
                    $q->where('nama_karyawan', 'like', "%{$search}%")
                        ->orWhere('nik_karyawan', 'like', "%{$search}%");
                })
                    ->orWhereHas('masterCuti', function ($q) use ($search) {
                        $q->where('uraian', 'like', "%{$search}%");
                    })
                    ->orWhere('jenis_cuti', 'like', "%{$search}%");
            });
        }

        // Apply date range filter
        if ($startDate) {
            $query->whereDate('tanggal_mulai_cuti', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_akhir_cuti', '<=', $endDate);
        }

        // Get counts for filter badges
        $totalCount = CutiKaryawan::count();
        $pendingCount = CutiKaryawan::where('status_acc', 'Menunggu Persetujuan')->count();
        $approvedCount = CutiKaryawan::where('status_acc', 'Disetujui')->count();
        $rejectedCount = CutiKaryawan::where('status_acc', 'Ditolak')->count();

        // Paginate results
        $cutiKaryawans = $query->paginate(10);

        return view('admin.cuti_karyawans.index', compact(
            'cutiKaryawans',
            'status',
            'search',
            'startDate',
            'endDate',
            'totalCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Menampilkan form untuk membuat pengajuan cuti baru
     */
    public function create()
    {
        $userDepartemen = auth()->user()->karyawan->departemen ?? null;
        $departemens = Departemen::all();
        $masterCutis = Mastercuti::all();
        $jenisCuti = ['Izin', 'Cuti'];

        if ($userDepartemen) {
            $karyawans = Karyawan::whereNull('tahun_keluar')
                ->where('id_departemen', $userDepartemen->id)
                ->get();
        } else {
            $karyawans = collect();
        }

        return view('admin.cuti_karyawans.create', compact(
            'userDepartemen',
            'departemens',
            'karyawans',
            'jenisCuti',
            'masterCutis'
        ));
    }

    public function getKaryawanByDepartemen($id)
    {
        try {
            $karyawans = Karyawan::whereNull('tahun_keluar')
                ->where('id_departemen', $id)
                ->select('id', 'nik_karyawan', 'nama_karyawan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $karyawans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data pengajuan cuti baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id',
            'jenis_cuti' => 'required|in:Izin,Cuti',
            'tanggal_mulai_cuti' => 'required|date',
            'tanggal_akhir_cuti' => 'required|date|after_or_equal:tanggal_mulai_cuti',
            'master_cuti_id' => 'nullable|exists:Mastercutis,id',
            'bukti' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'id_supervisor' => 'required|exists:karyawans,id',
        ]);

        // Hitung jumlah hari cuti
        $startDate = Carbon::parse($request->tanggal_mulai_cuti);
        $endDate = Carbon::parse($request->tanggal_akhir_cuti);
        $jumlahHari = $startDate->diffInDays($endDate) + 1; // Termasuk hari awal dan akhir

        $data = $request->all();
        $data['jumlah_hari_cuti'] = $jumlahHari;
        $data['status_acc'] = 'Menunggu Persetujuan';

        // Proses upload file bukti
        if ($request->hasFile('bukti')) {
            $bukti = $request->file('bukti');
            $buktiName = 'cuti-' . Str::slug($request->jenis_cuti) . '-' . time() . '.' . $bukti->getClientOriginalExtension();
            $bukti->storeAs('public/cuti/bukti', $buktiName);
            $data['bukti'] = $buktiName;
        }

        // Simpan data cuti ke database
        CutiKaryawan::create($data);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('cuti_karyawans.index')
            ->with('success', 'Pengajuan Cuti berhasil ditambahkan');
    }

    /**
     * Menampilkan detail pengajuan cuti tertentu
     */
    public function show(CutiKaryawan $cutiKaryawan)
    {
        // Muat relasi yang dibutuhkan untuk detail cuti
        $cutiKaryawan->load(['karyawan', 'masterCuti', 'supervisor', 'approver']);
        return view('admin.cuti_karyawans.show', compact('cutiKaryawan'));
    }

    /**
     * Menampilkan form untuk mengedit pengajuan cuti
     */
    public function edit(CutiKaryawan $cutiKaryawan)
    {
        // Siapkan data untuk dropdown di form edit
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        $masterCutis = Mastercuti::where('cuti_max', '!=', null)->orderBy('uraian')->get();
        $jenisCuti = ['Izin', 'Cuti'];

        // Muat relasi yang dibutuhkan
        $cutiKaryawan->load(['karyawan', 'masterCuti', 'supervisor', 'approver']);

        return view('admin.cuti_karyawans.edit', compact('cutiKaryawan', 'karyawans', 'masterCutis', 'jenisCuti'));
    }

    /**
     * Memperbarui data pengajuan cuti di database
     */
    public function update(Request $request, CutiKaryawan $cutiKaryawan)
    {
        // Validasi input dari form edit
        $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id',
            'jenis_cuti' => 'required|in:Izin,Cuti',
            'tanggal_mulai_cuti' => 'required|date',
            'tanggal_akhir_cuti' => 'required|date|after_or_equal:tanggal_mulai_cuti',
            'master_cuti_id' => 'nullable|exists:Mastercutis,id',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'id_supervisor' => 'required|exists:karyawans,id',
        ]);

        // Hitung jumlah hari cuti
        $startDate = Carbon::parse($request->tanggal_mulai_cuti);
        $endDate = Carbon::parse($request->tanggal_akhir_cuti);
        $jumlahHari = $startDate->diffInDays($endDate) + 1; // Termasuk hari awal dan akhir

        $data = $request->all();
        $data['jumlah_hari_cuti'] = $jumlahHari;

        // Cek apakah pengajuan masih bisa diedit (status masih menunggu)
        if ($cutiKaryawan->status_acc != 'Menunggu Persetujuan') {
            return redirect()->route('cuti_karyawans.index')
                ->with('error', 'Tidak dapat mengedit pengajuan yang sudah diproses');
        }

        // Proses upload file bukti baru jika ada
        if ($request->hasFile('bukti')) {
            // Hapus file bukti lama jika ada
            if ($cutiKaryawan->bukti) {
                Storage::delete('public/cuti/bukti/' . $cutiKaryawan->bukti);
            }

            $bukti = $request->file('bukti');
            $buktiName = 'cuti-' . Str::slug($request->jenis_cuti) . '-' . time() . '.' . $bukti->getClientOriginalExtension();
            $bukti->storeAs('public/cuti/bukti', $buktiName);
            $data['bukti'] = $buktiName;
        }

        // Update data cuti di database
        $cutiKaryawan->update($data);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('cuti_karyawans.index')
            ->with('success', 'Pengajuan Cuti berhasil diupdate');
    }

    /**
     * Menghapus data pengajuan cuti dari database
     */
    public function destroy(CutiKaryawan $cutiKaryawan)
    {
        // Cek apakah pengajuan masih bisa dihapus (status masih menunggu)
        if ($cutiKaryawan->status_acc != 'Menunggu Persetujuan') {
            return redirect()->route('cuti_karyawans.index')
                ->with('error', 'Tidak dapat menghapus pengajuan yang sudah diproses');
        }

        // Hapus file bukti jika ada
        if ($cutiKaryawan->bukti) {
            Storage::delete('public/cuti/bukti/' . $cutiKaryawan->bukti);
        }

        // Hapus data cuti
        $cutiKaryawan->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('cuti_karyawans.index')
            ->with('success', 'Pengajuan Cuti berhasil dihapus');
    }

    /**
     * Memproses persetujuan atau penolakan pengajuan cuti
     */
    public function approve(Request $request, CutiKaryawan $cutiKaryawan)
    {
        // Validasi input dari form approval
        $request->validate([
            'status_acc' => 'required|in:Disetujui,Ditolak',
            'keterangan_tolak' => 'required_if:status_acc,Ditolak',
            'cuti_disetujui' => 'required_if:status_acc,Disetujui',
        ]);

        // Get the authenticated user's karyawan record
        $karyawanId = null;
        $user = Auth::user();
        if ($user) {
            // Find the karyawan record associated with the authenticated user
            $karyawan = Karyawan::where('user_id', $user->id)->first();
            if ($karyawan) {
                $karyawanId = $karyawan->id;
            }
        }

        // Update detail persetujuan
        $cutiKaryawan->status_acc = $request->status_acc;
        $cutiKaryawan->keterangan_tolak = $request->status_acc == 'Ditolak' ? $request->keterangan_tolak : null;
        $cutiKaryawan->cuti_disetujui = $request->status_acc == 'Disetujui' ? $request->cuti_disetujui : null;
        $cutiKaryawan->tanggal_approval = now();
        $cutiKaryawan->approved_by = $karyawanId; // Use the karyawan ID instead of user ID
        $cutiKaryawan->save();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('cuti_karyawans.index')
            ->with('success', 'Pengajuan Cuti berhasil ' . strtolower($request->status_acc));
    }

    /**
     * Menampilkan form untuk persetujuan pengajuan cuti
     */
    public function approvalForm(CutiKaryawan $cutiKaryawan)
    {
        // Muat relasi yang dibutuhkan untuk form approval
        $cutiKaryawan->load(['karyawan', 'masterCuti', 'supervisor']);
        return view('admin.cuti_karyawans.approval', compact('cutiKaryawan'));
    }
}

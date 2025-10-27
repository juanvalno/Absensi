<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodeGaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PeriodeGajiController extends Controller
{

    /**
     * Menampilkan daftar semua periode gaji
     */
    public function index()
    {
        // Ambil semua data periode gaji dan urutkan berdasarkan tanggal mulai
        $periodeGajis = PeriodeGaji::orderBy('tanggal_mulai', 'asc')->get();
        return view('admin.periodegajis.index', compact('periodeGajis'));
    }

    /**
     * Menampilkan form untuk membuat periode gaji baru
     */
    public function create()
    {
        return view('admin.periodegajis.create');
    }

    /**
     * Menyimpan data periode gaji baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'nama_periode'    => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
            'keterangan'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Jika periode ini diatur sebagai aktif, nonaktifkan semua periode lainnya
        if ($request->status == 'aktif') {
            PeriodeGaji::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        // Simpan data periode gaji ke database
        PeriodeGaji::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', 'Periode Gaji berhasil ditambahkan');
    }

    /**
     * Menampilkan detail periode gaji tertentu
     */
    public function show(PeriodeGaji $periodegaji)
    {
        return view('admin.periodegajis.show', compact('periodegaji'));
    }

    /**
     * Menampilkan form untuk mengedit periode gaji
     */
    public function edit(PeriodeGaji $periodegaji)
    {
        return view('admin.periodegajis.edit', compact('periodegaji'));
    }

    /**
     * Memperbarui data periode gaji di database
     */
    public function update(Request $request, PeriodeGaji $periodegaji)
    {
        // Validasi input dari form edit
        $validator = Validator::make($request->all(), [
            'nama_periode'    => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
            'keterangan'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Jika periode ini diatur sebagai aktif dan sebelumnya tidak aktif, nonaktifkan semua periode lainnya
        if ($request->status == 'aktif' && $periodegaji->status != 'aktif') {
            PeriodeGaji::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        // Update data periode gaji di database
        $periodegaji->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', 'Periode Gaji berhasil diperbarui');
    }

    /**
     * Menghapus data periode gaji dari database
     */
    public function destroy(PeriodeGaji $periodegaji)
    {
        // Periksa apakah periode sedang digunakan untuk penggajian
        if ($periodegaji->hasPayrollData()) {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Periode Gaji tidak dapat dihapus karena sedang digunakan pada data penggajian');
        }

        // Tidak mengizinkan penghapusan periode aktif
        if ($periodegaji->status == 'aktif') {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Periode Gaji aktif tidak dapat dihapus. Silakan nonaktifkan terlebih dahulu.');
        }

        // Hapus data periode gaji
        $periodegaji->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', 'Periode Gaji berhasil dihapus');
    }

    /**
     * Mengatur periode sebagai aktif
     */
    public function setActive(PeriodeGaji $periodegaji)
    {
        // Log untuk debugging
        \Log::info('Attempting to activate period: ' . $periodegaji->nama_periode . ' (ID: ' . $periodegaji->id . ')');

        try {
            // Menggunakan method di model untuk mengatur periode sebagai aktif
            $periodegaji->setAsActive();

            // Log success
            \Log::info('Period activated successfully');

            // Redirect ke halaman index dengan pesan sukses
            return redirect()->route('periodegaji.index')
                ->with('success', 'Periode Gaji berhasil diaktifkan');
        } catch (\Exception $e) {
            // Log error
            \Log::error('Failed to activate period: ' . $e->getMessage());

            return redirect()->route('periodegaji.index')
                ->with('error', 'Gagal mengaktifkan periode: ' . $e->getMessage());
        }
    }

    /**
     * Membuat periode bulanan otomatis untuk satu tahun
     */
    public function generateMonthly(Request $request)
    {
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'year'      => 'required|integer|min:2000|max:2100',
            'start_day' => 'required|integer|min:1|max:28',
            'end_day'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $year     = $request->year;
        $startDay = $request->start_day;
        $endDay   = $request->end_day;
        $count    = 0;

        // Loop untuk setiap bulan dalam tahun
        for ($month = 1; $month <= 12; $month++) {
            // Buat tanggal mulai dengan hari yang ditentukan
            $startDate = Carbon::createFromDate($year, $month, $startDay);

            // Buat tanggal akhir berdasarkan pilihan
            if ($endDay === 'end_of_month') {
                // Jika dipilih akhir bulan
                $endDate = $startDate->copy()->endOfMonth();
            } else {
                // Jika hari akhir yang dipilih, selalu gunakan bulan berikutnya jika hari akhir <= hari mulai
                if ((int)$endDay <= $startDay) {
                    $endMonth = $month < 12 ? $month + 1 : 1;
                    $endYear  = $month < 12 ? $year : $year + 1;
                    $endDate  = Carbon::createFromDate($endYear, $endMonth, (int)$endDay);
                } else {
                    $endDate = Carbon::createFromDate($year, $month, (int)$endDay);
                }
            }

            // Format nama periode berdasarkan rentang tanggal, bukan hanya bulan
            $periodeName = 'Gaji Bulanan ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');

            // Periksa apakah periode sudah ada
            $exists = PeriodeGaji::where('nama_periode', $periodeName)->exists();

            // Buat periode baru jika belum ada
            if (!$exists) {
                PeriodeGaji::create([
                    'nama_periode'    => $periodeName,
                    'tanggal_mulai'   => $startDate->format('Y-m-d'),
                    'tanggal_selesai' => $endDate->format('Y-m-d'),
                    'status'          => 'nonaktif',
                    'keterangan'      => 'Periode gaji bulanan yang dibuat otomatis',
                ]);
                $count++;
            }
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', "Berhasil membuat $count periode gaji bulanan untuk tahun $year");
    }

    /**
     * Membuat periode mingguan otomatis untuk satu bulan
     */
    public function generateWeekly(Request $request)
    {
        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'year'              => 'required|integer|min:2000|max:2100',
            'month'             => 'required|integer|min:1|max:12',
            'start_day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $year           = $request->year;
        $month          = $request->month;
        $startDayOfWeek = $request->start_day_of_week;
        $count          = 0;

        // Dapatkan hari pertama bulan
        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1);

        // Temukan kemunculan pertama dari hari yang dipilih
        $currentDate = $firstDayOfMonth->copy();
        while (strtolower($currentDate->englishDayOfWeek) !== $startDayOfWeek) {
            $currentDate->addDay();
        }

        // Buat periode mingguan sampai melewati akhir bulan
        while (
            $currentDate->month == $month ||
            ($currentDate->month != $month && $currentDate->copy()->addDays(6)->month == $month)
        ) {

            $startDate = $currentDate->copy();
            $endDate   = $startDate->copy()->addDays(6);  // Periode 7 hari (termasuk hari mulai)

            $periodeName = 'Gaji Mingguan ' . $startDate->format('d') . ' - ' . $endDate->format('d M Y');

            // Periksa apakah periode sudah ada
            $exists = PeriodeGaji::where('nama_periode', $periodeName)->exists();

            // Buat periode baru jika belum ada
            if (!$exists) {
                PeriodeGaji::create([
                    'nama_periode'    => $periodeName,
                    'tanggal_mulai'   => $startDate->format('Y-m-d'),
                    'tanggal_selesai' => $endDate->format('Y-m-d'),
                    'status'          => 'nonaktif',
                    'keterangan'      => 'Periode gaji mingguan yang dibuat otomatis',
                ]);
                $count++;
            }

            // Pindah ke minggu berikutnya
            $currentDate->addDays(7);
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', "Berhasil membuat $count periode gaji mingguan untuk bulan " . $firstDayOfMonth->format('F Y'));
    }

    /**
     * Menghapus beberapa periode sekaligus
     */
    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

        // Periksa apakah ada periode yang dipilih
        if (empty($ids)) {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Tidak ada periode yang dipilih');
        }

        // Periksa apakah ada periode aktif yang dipilih
        $hasActive = PeriodeGaji::whereIn('id', $ids)
            ->where('status', 'aktif')
            ->exists();

        if ($hasActive) {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Periode Gaji aktif tidak dapat dihapus. Silakan nonaktifkan terlebih dahulu.');
        }

        // Periksa apakah ada periode yang memiliki data penggajian
        $hasPayrollData = false;
        foreach ($ids as $id) {
            $periode = PeriodeGaji::find($id);
            if ($periode && $periode->hasPayrollData()) {
                $hasPayrollData = true;
                break;
            }
        }

        if ($hasPayrollData) {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Beberapa periode tidak dapat dihapus karena sedang digunakan pada data penggajian');
        }

        // Hapus periode yang dipilih
        PeriodeGaji::whereIn('id', $ids)->delete();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', 'Periode Gaji yang dipilih berhasil dihapus');
    }

    /**
     * Mengatur periode yang dipilih sebagai aktif
     */
    public function setActiveMultiple(Request $request)
    {
        $ids = $request->ids;

        // Periksa apakah ada periode yang dipilih
        if (empty($ids) || count($ids) != 1) {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Pilih tepat satu periode untuk diaktifkan');
        }

        // Ambil periode yang dipilih
        $periode = PeriodeGaji::find($ids[0]);

        if (!$periode) {
            return redirect()->route('periodegaji.index')
                ->with('error', 'Periode tidak ditemukan');
        }

        // Menggunakan method di model untuk mengatur periode sebagai aktif
        $periode->setAsActive();

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('periodegaji.index')
            ->with('success', 'Periode Gaji berhasil diaktifkan');
    }
}

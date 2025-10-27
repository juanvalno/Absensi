public function createHoliday(HariLibur $harilibur)
{
    try {
        DB::beginTransaction();

        // Get all active employees
        $karyawans = Karyawan::where('status', 'Aktif')->get();

        // Create holiday absence records for each employee
        foreach ($karyawans as $karyawan) {
            Absensi::create([
                'karyawan_id' => $karyawan->id,
                'tanggal' => $harilibur->tanggal,
                'status' => 'Libur',
                'keterangan' => $harilibur->nama_libur,
                'jenis_absensi' => 'System'
            ]);
        }

        DB::commit();
        return redirect()->route('absensis.index')
            ->with('success', 'Berhasil membuat absensi libur untuk semua karyawan.');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->route('absensis.index')
            ->with('error', 'Gagal membuat absensi libur: ' . $e->getMessage());
    }
}
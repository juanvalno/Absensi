public function index()
{
    $allCount = Karyawan::count();
    $bulananCount = Karyawan::where('statuskaryawan', 'Bulanan')->whereNull('tahun_keluar')->count();
    $harianCount = Karyawan::where('statuskaryawan', 'Harian')->whereNull('tahun_keluar')->count();
    $boronganCount = Karyawan::where('statuskaryawan', 'Borongan')->whereNull('tahun_keluar')->count();
    $resignCount = Karyawan::whereNotNull('tahun_keluar')->count();
    $activeCount = $allCount - $resignCount;

    return view('admin.dashboard', compact(
        'activeCount',
        'resignCount',
        'bulananCount',
        'harianCount',
        'boronganCount'
    ));
}
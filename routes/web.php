<?php

use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\BagianController;
use App\Http\Controllers\Admin\LemburController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\ProfesiController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\HariliburController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Admin\MastercutiController;
use App\Http\Controllers\Admin\PenggajianController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleAccessController;
use App\Http\Controllers\Admin\UangTungguController;
use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Admin\JadwalKerjaController;
use App\Http\Controllers\Admin\MastershiftController;
use App\Http\Controllers\Admin\PeriodeGajiController;
use App\Http\Controllers\Admin\CutiKaryawanController;
use App\Http\Controllers\Admin\MesinAbsensiController;
use App\Http\Controllers\Admin\ProgramStudiController;
use App\Http\Controllers\Admin\KuotaCutiTahunanController;
use App\Http\Controllers\Admin\KuotaIzinTahunanController;
use App\Http\Controllers\Admin\SpecialPermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//------------------------------------------------------------------
// Public Routes
//------------------------------------------------------------------

// Redirect root to login page
Route::get('/', function () {
    return redirect('/login');
});

// Global Karyawan routes
Route::get('/get-karyawan-status/{id}', [KaryawanController::class, 'getStatus']);
Route::get('/karyawans/get-all-active', [KaryawanController::class, 'getAllActive'])
    ->name('karyawans.get-all-active');
Route::get('karyawans/search', [KaryawanController::class, 'search'])
    ->name('karyawans.search');
Route::get('/get-bagian', [BagianController::class, 'getBagianByDepartemen'])->name('get.bagian');
Route::get('shifts/getNextCode', [ShiftController::class, 'getNextCode'])->name('shifts.getNextCode');
Route::get('/get-program-studi', function (Request $request) {
    $educationType = $request->query('education_type');

    // Debugging
    if (!$educationType) {
        return response()->json(['error' => 'education_type tidak ditemukan'], 400);
    }

    $programStudi = ProgramStudi::where('education_type', $educationType)->get(['id', 'name_programstudi']);

    if ($programStudi->isEmpty()) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    return response()->json($programStudi, 200, [], JSON_NUMERIC_CHECK);
});

//------------------------------------------------------------------
// Protected Routes (Auth Required)
//------------------------------------------------------------------
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        // Existing counts
        $allCount = \App\Models\Karyawan::count();
        $bulananCount = \App\Models\Karyawan::where('statuskaryawan', 'Bulanan')->whereNull('tahun_keluar')->count();
        $harianCount = \App\Models\Karyawan::where('statuskaryawan', 'Harian')->whereNull('tahun_keluar')->count();
        $boronganCount = \App\Models\Karyawan::where('statuskaryawan', 'Borongan')->whereNull('tahun_keluar')->count();
        $resignCount = \App\Models\Karyawan::whereNotNull('tahun_keluar')->count();
        $activeCount = $allCount - $resignCount;

        // Get yearly data
        $yearlyData = \App\Models\Karyawan::selectRaw('YEAR(tgl_awalmmasuk) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $yearlyResign = \App\Models\Karyawan::whereNotNull('tahun_keluar')
            ->selectRaw('YEAR(tahun_keluar) as year, COUNT(*) as total')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return view('admin.dashboard', compact(
            'activeCount',
            'resignCount',
            'bulananCount',
            'harianCount',
            'boronganCount',
            'yearlyData',
            'yearlyResign'
        ));
    })->name('admin.dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::get('/profile/edit', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
    });

    //------------------------------------------------------------------
    // User Management
    //------------------------------------------------------------------

    // Users
    Route::middleware('permission.check:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password')
        ->middleware('auth');

    Route::middleware('permission.check:users.create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware('permission.check:users.edit')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    Route::middleware('permission.check:users.delete')->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Roles
    Route::middleware('permission.check:roles.view')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    });

    Route::middleware('permission.check:roles.create')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::middleware('permission.check:roles.edit')->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::middleware('permission.check:roles.delete')->group(function () {
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Permissions
    Route::middleware('permission.check:permissions.view')->group(function () {
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    });

    Route::get('permissions/update-db', [PermissionController::class, 'updatePermissions'])
        ->name('permissions.update-db');

    Route::middleware('permission.check:permissions.create')->group(function () {
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    });

    Route::middleware('permission.check:permissions.edit')->group(function () {
        Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    });

    Route::middleware('permission.check:permissions.delete')->group(function () {
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Menus
    Route::middleware('permission.check:menu.view')->group(function () {
        Route::get('menu', [MenuController::class, 'index'])->name('menu.index');
    });

    Route::middleware('permission.check:menu.create')->group(function () {
        Route::get('menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('menu', [MenuController::class, 'store'])->name('menu.store');
    });

    Route::middleware('permission.check:menu.edit')->group(function () {
        Route::get('menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::post('menu/update-order', [MenuController::class, 'updateOrder'])->name('menu.update-order');
    });

    Route::middleware('permission.check:menu.delete')->group(function () {
        Route::delete('menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
    });

    // Role Access Management
    Route::middleware('permission.check:roles.view')->group(function () {
        Route::get('role-access', [RoleAccessController::class, 'index'])->name('role-access.index');
    });

    Route::middleware('permission.check:roles.edit')->group(function () {
        Route::post('role-access/{role}', [RoleAccessController::class, 'update'])->name('role-access.update');
        Route::post('role-access/{role}/copy', [RoleAccessController::class, 'copyPermissions'])->name('role-access.copy-permissions');
    });

    // User Access Management
    Route::middleware('permission.check:users.view')->group(function () {
        Route::get('user-access', [UserAccessController::class, 'index'])->name('user-access.index');
    });

    Route::middleware('permission.check:users.edit')->group(function () {
        Route::post('user-access/{user}', [UserAccessController::class, 'update'])->name('user-access.update');
        Route::post('user-access/{user}/copy', [UserAccessController::class, 'copyAccess'])->name('user-access.copy-access');
    });

    //------------------------------------------------------------------
    // Organization Structure
    //------------------------------------------------------------------

    // Departemen
    Route::resource('departemens', DepartemenController::class);

    // Bagian
    Route::resource('bagians', BagianController::class);

    // Program Studi
    Route::resource('program_studis', ProgramStudiController::class);

    // Profesi
    Route::resource('profesis', ProfesiController::class);

    // Jabatan
    Route::resource('jabatans', JabatanController::class);

    //------------------------------------------------------------------
    // Employee Management
    //------------------------------------------------------------------

    // Karyawan
    Route::resource('karyawans', KaryawanController::class);
    Route::get('karyawans/get-bagians/{id_departemen}', [KaryawanController::class, 'getBagiansByDepartemen'])
        ->name('karyawans.get-bagians');
    Route::get('/get-nik', [KaryawanController::class, 'getNik'])->name('karyawans.get-nik');
    Route::patch('/karyawans/{karyawan}/resign', [KaryawanController::class, 'resign'])->name('karyawans.resign');

    //------------------------------------------------------------------
    // Time Management
    //------------------------------------------------------------------

    // Hari Libur
    Route::resource('hariliburs', HariliburController::class);
    Route::get('hariliburs-generate-sundays', [HariliburController::class, 'generateSundaysForm'])
        ->name('hariliburs.generate-sundays-form');
    Route::post('hariliburs-generate-sundays', [HariliburController::class, 'generateSundays'])
        ->name('hariliburs.generate-sundays');

    // Shifts
    Route::resource('shifts', ShiftController::class);

    // Jadwal Kerja
    Route::resource('jadwalkerjas', JadwalKerjaController::class);
    Route::get('jadwalkerjas/report', [JadwalKerjaController::class, 'report'])->name('jadwalkerjas.report');

    // Master Cuti
    Route::resource('mastercutis', MastercutiController::class);

    // Kuota Cuti Tahunan Routes
    Route::get('kuota-cuti', [KuotaCutiTahunanController::class, 'index'])->name('kuota-cuti.index');
    Route::get('kuota-cuti/report', [KuotaCutiTahunanController::class, 'report'])->name('kuota-cuti.report');
    Route::get('kuota-cuti/create', [KuotaCutiTahunanController::class, 'create'])->name('kuota-cuti.create');
    Route::post('kuota-cuti', [KuotaCutiTahunanController::class, 'store'])->name('kuota-cuti.store');
    Route::get('kuota-cuti/{id}/edit', [KuotaCutiTahunanController::class, 'edit'])->name('kuota-cuti.edit');
    Route::put('kuota-cuti/{id}', [KuotaCutiTahunanController::class, 'update'])->name('kuota-cuti.update');
    Route::delete('kuota-cuti/{id}', [KuotaCutiTahunanController::class, 'destroy'])->name('kuota-cuti.destroy');
    Route::post('/kuota-cuti/generate-massal', [KuotaCutiTahunanController::class, 'generateMassal'])->name('kuota-cuti.generate-massal');

    // Kuota Izin Tahunan Routes
    Route::get('kuota-izin', [KuotaIzinTahunanController::class, 'index'])->name('kuota-izin.index');
    Route::get('kuota-izin/report', [KuotaIzinTahunanController::class, 'report'])->name('kuota-izin.report');
    Route::get('kuota-izin/create', [KuotaIzinTahunanController::class, 'create'])->name('kuota-izin.create');
    Route::post('kuota-izin', [KuotaIzinTahunanController::class, 'store'])->name('kuota-izin.store');
    Route::get('kuota-izin/{id}', [KuotaIzinTahunanController::class, 'show'])->name('kuota-izin.show');
    Route::get('kuota-izin/{id}/edit', [KuotaIzinTahunanController::class, 'edit'])->name('kuota-izin.edit');
    Route::put('kuota-izin/{id}', [KuotaIzinTahunanController::class, 'update'])->name('kuota-izin.update');
    Route::delete('kuota-izin/{id}', [KuotaIzinTahunanController::class, 'destroy'])->name('kuota-izin.destroy');
    Route::post('kuota-izin/generate-massal', [KuotaIzinTahunanController::class, 'generateMassal'])->name('kuota-izin.generate-massal');

    // Cuti Karyawan routes
    Route::resource('cuti_karyawans', CutiKaryawanController::class);
    Route::get('get-karyawan-by-departemen/{id}', [CutiKaryawanController::class, 'getKaryawanByDepartemen'])
        ->name('get.karyawan.by.departemen');
    Route::get('get-supervisors-by-departemen/{id}', [KaryawanController::class, 'getSupervisorsByDepartemen'])
        ->name('get.supervisors.by.departemen');
    Route::get('cuti_karyawans/{cutiKaryawan}/approval', [CutiKaryawanController::class, 'approvalForm'])
        ->name('cuti_karyawans.approval');
    Route::post('cuti_karyawans/{cutiKaryawan}/approve', [CutiKaryawanController::class, 'approve'])->name('cuti_karyawans.approve');

    // Lembur
    Route::resource('lemburs', LemburController::class);
    Route::get('lemburs/{lembur}/approval', [LemburController::class, 'approvalForm'])
        ->name('lemburs.approval');
    Route::post('lemburs/{lembur}/approve', [LemburController::class, 'approve'])
        ->name('lemburs.approve');

    //------------------------------------------------------------------
    // Attendance Management
    //------------------------------------------------------------------

    // Absensi
    Route::resource('absensis', AbsensiController::class);
    Route::get('/absensis/create-holiday/{harilibur}', [AbsensiController::class, 'createHoliday'])
        ->name('absensis.create-holiday')
        ->middleware('auth');
    Route::get('absensis/check-schedule', [AbsensiController::class, 'checkSchedule'])
        ->name('absensis.check-schedule');
    Route::get('absensis/fetch', [AbsensiController::class, 'showFetchForm'])
        ->name('absensis.fetch.form');
    Route::post('absensis/fetch', [AbsensiController::class, 'fetchData'])
        ->name('absensis.fetch.process');
    Route::get('absensis/sync', [AbsensiController::class, 'startSync'])
        ->name('absensis.sync');
    Route::get('absensis/report/daily', [AbsensiController::class, 'dailyReport'])
        ->name('admin.absensis.daily-report');
    Route::get('absensis/getTodaySummary', [AbsensiController::class, 'getTodaySummary'])
        ->name('admin.absensis.getTodaySummary');
    Route::get('absensis/fetchLatestData', [AbsensiController::class, 'fetchLatestData'])
        ->name('admin.absensis.fetchLatestData');
    Route::get('absensis/report/employee', [AbsensiController::class, 'employeeReport'])
        ->name('absensis.report.employee');
    Route::post('absensis/checkout', [AbsensiController::class, 'checkout'])
        ->name('absensis.checkout');

    // Mesin Absensi
    Route::resource('mesinabsensis', MesinAbsensiController::class);

    // Mesin Absensi - Basic Operations
    Route::put('mesinabsensis/{mesinabsensi}/toggle-status', [MesinAbsensiController::class, 'toggleStatus'])
        ->name('mesinabsensis.toggle-status');
    Route::get('mesinabsensis/{mesinabsensi}/test-connection', [MesinAbsensiController::class, 'testConnection'])
        ->name('mesinabsensis.test-connection');
    Route::get('mesinabsensis/{mesinabsensi}/auto-detect-ip', [MesinAbsensiController::class, 'autoDetectIp'])
        ->name('mesinabsensis.auto-detect-ip');

    // Mesin Absensi - User Management
    Route::get('mesinabsensis/{mesinabsensi}/get-registered-users', [MesinAbsensiController::class, 'getRegisteredUsers'])
        ->name('mesinabsensis.get-registered-users');
    Route::post('mesinabsensis/{mesinabsensi}/delete-user', [MesinAbsensiController::class, 'deleteUser'])
        ->name('mesinabsensis.delete-user');
    Route::post('mesinabsensis/clone-users', [MesinAbsensiController::class, 'cloneUsers'])
        ->name('mesinabsensis.clone-users');
    Route::post('mesinabsensis/sync-all-users', [MesinAbsensiController::class, 'syncAllUsers'])
        ->name('mesinabsensis.sync-all-users');

    // Mesin Absensi - Log Management
    Route::get('mesinabsensis/{mesinabsensi}/download-logs', [MesinAbsensiController::class, 'downloadLogs'])
        ->name('mesinabsensis.download-logs');
    Route::get('mesinabsensis/download-logs-range', [MesinAbsensiController::class, 'downloadLogsRange'])
        ->name('mesinabsensis.download-logs-range');
    Route::get('mesinabsensis/download-logs-user', [MesinAbsensiController::class, 'downloadLogsUser'])
        ->name('mesinabsensis.download-logs-user');
    Route::post('mesinabsensis/{mesinabsensi}/process-logs', [MesinAbsensiController::class, 'processLogs'])
        ->name('mesinabsensis.process-logs');
    Route::post('mesinabsensis/{mesinabsensi}/upload-direct-batch', [MesinAbsensiController::class, 'uploadDirectBatch'])
        ->name('mesinabsensis.upload-direct-batch');

    // Mesin Absensi - Name Management
    Route::get('mesinabsensis/{mesinabsensi}/upload-names', [MesinAbsensiController::class, 'showUploadNames'])
        ->name('mesinabsensis.upload-names');
    Route::post('mesinabsensis/{mesinabsensi}/upload-names', [MesinAbsensiController::class, 'uploadNames'])
        ->name('mesinabsensis.upload-names-store');
    Route::post('mesinabsensis/{mesinabsensi}/upload-names-batch', [MesinAbsensiController::class, 'uploadNamesBatch'])
        ->name('mesinabsensis.upload-names-batch');

    //------------------------------------------------------------------
    // Payroll Management
    //------------------------------------------------------------------

    // Uang Tunggu
    Route::resource('uangtunggus', UangTungguController::class);

    // Potongan
    Route::resource('potongans', \App\Http\Controllers\Admin\PotonganController::class);

    // Periode Gaji
    Route::resource('periodegaji', PeriodeGajiController::class);
    Route::post('periodegaji/generate-monthly', [PeriodeGajiController::class, 'generateMonthly'])
        ->name('periodegaji.generate-monthly');
    Route::post('periodegaji/generate-weekly', [PeriodeGajiController::class, 'generateWeekly'])
        ->name('periodegaji.generate-weekly');
    Route::post('periodegaji/delete-multiple', [PeriodeGajiController::class, 'deleteMultiple'])
        ->name('periodegaji.delete-multiple');
    Route::put('periodegaji/{periodegaji}/set-active', [PeriodeGajiController::class, 'setActive'])
        ->name('periodegaji.set-active');

    // Penggajian - Mass payslip routes (must be first)
    Route::get('penggajian/mass-payslip', [PenggajianController::class, 'massPayslip'])
        ->name('penggajian.mass-payslip');
    Route::post('penggajian/process-mass-payslip', [PenggajianController::class, 'processMassPayslip'])
        ->name('penggajian.process-mass-payslip');
    Route::get('/penggajian/riwayat-ajuan', [PenggajianController::class, 'salarySubmissionHistory'])
        ->name('penggajian.salarySubmissionHistory');
    Route::get('penggajian/export-all-history', [PenggajianController::class, 'exportAllHistoryExcel'])
        ->name('penggajian.exportAllHistoryExcel');
    Route::get('penggajian/export-history/{id}', [PenggajianController::class, 'exportHistoryExcel'])
        ->name('penggajian.exportHistoryExcel');

    // Penggajian - Payslips
    Route::get('penggajian/{id}/payslip', [PenggajianController::class, 'generatePayslip'])
        ->name('penggajian.payslip');
    Route::get('penggajian/periode/{periodeId}/payslips', [PenggajianController::class, 'generatePayslips'])
        ->name('penggajian.payslips');

    // Penggajian - Data processing
    Route::post('penggajian/get-filtered-karyawans', [PenggajianController::class, 'getFilteredKaryawans'])
        ->name('penggajian.getFilteredKaryawans');
    Route::post('penggajian/batch-process', [PenggajianController::class, 'batchProcess'])
        ->name('penggajian.batchProcess');
    Route::post('penggajian/review', [PenggajianController::class, 'review'])
        ->name('penggajian.review');
    Route::post('penggajian/process', [PenggajianController::class, 'process'])
        ->name('penggajian.process');
    Route::post('penggajian/send-to-finance', [PenggajianController::class, 'sendToFinance'])
        ->name('penggajian.sendToFinance');

    // Penggajian - Component management
    Route::post('penggajian/{penggajian}/add-component', [PenggajianController::class, 'addComponent'])
        ->name('penggajian.addComponent');
    Route::delete('penggajian/{penggajian}/remove-component', [PenggajianController::class, 'removeComponent'])
        ->name('penggajian.removeComponent');

    // Penggajian - Reports
    Route::get('penggajian-report/by-period', [PenggajianController::class, 'reportByPeriod'])
        ->name('penggajian.reportByPeriod');
    Route::get('penggajian-report/by-department', [PenggajianController::class, 'reportByDepartment'])
        ->name('penggajian.reportByDepartment');
    Route::get('penggajian-report/export-excel', [PenggajianController::class, 'exportExcel'])
        ->name('penggajian.exportExcel');

    // Penggajian - Base resource route (must be last)
    Route::resource('penggajian', PenggajianController::class);

    // Keuangan routes
    Route::get('keuangan/export/{id}', [KeuanganController::class, 'export'])
        ->name('keuangan.export');
    Route::get('keuangan', [KeuanganController::class, 'index'])
        ->name('keuangan.index');
    Route::get('keuangan/{id}', [KeuanganController::class, 'show'])
        ->name('keuangan.show');
    Route::post('keuangan/{id}/approve', [KeuanganController::class, 'approve'])
        ->name('keuangan.approve');
    Route::post('keuangan/{id}/reject', [KeuanganController::class, 'reject'])
        ->name('keuangan.reject');
});

// Authentication Routes
require __DIR__ . '/auth.php';

// Route untuk debugging
Route::get('/routes', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => $route->methods(),
            'action' => $route->getActionName(),
        ];
    });

    return response()->json($routes);
});

Route::get('/admin/employee-statistics', function () {
    $years = \App\Models\Karyawan::selectRaw('YEAR(tgl_awalmmasuk) as year')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->get()
        ->pluck('year');

    $selectedYear = request('year', now()->year);

    // Get monthly data for new employees and resignations
    $monthlyData = \DB::table('karyawans as k1')
        ->selectRaw('
            MONTH(tgl_awalmmasuk) as month,
            COUNT(*) as new_employees,
            (
                SELECT COUNT(*)
                FROM karyawans k2
                WHERE YEAR(k2.tahun_keluar) = ?
                AND MONTH(k2.tahun_keluar) = MONTH(tgl_awalmmasuk)
                AND k2.tahun_keluar IS NOT NULL
            ) as resignations,
            (
                SELECT COUNT(*)
                FROM karyawans k3
                WHERE YEAR(k3.tgl_awalmmasuk) <= ?
                AND (k3.tahun_keluar IS NULL OR YEAR(k3.tahun_keluar) > ?)
                AND MONTH(k3.tgl_awalmmasuk) <= MONTH(tgl_awalmmasuk)
            ) as active_employees
        ', [$selectedYear, $selectedYear, $selectedYear])
        ->where(DB::raw('YEAR(tgl_awalmmasuk)'), $selectedYear)
        ->groupBy(\DB::raw('MONTH(tgl_awalmmasuk)'))
        ->orderBy('month')
        ->get();

    return view('admin.employee-statistics', compact('years', 'selectedYear', 'monthlyData'));
})->name('admin.employee-statistics');

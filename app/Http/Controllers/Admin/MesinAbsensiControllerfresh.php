<?php

namespace App\Http\Controllers\Admin;

use App\Models\MesinAbsensi;
use App\Models\Karyawan;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MesinAbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mesinAbsensis = MesinAbsensi::orderBy('nama')->get();
        return view('admin.mesinabsensis.index', compact('mesinAbsensis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mesinabsensis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat_ip' => 'required|string|max:255',
            'kunci_komunikasi' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'status_aktif' => 'boolean',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Set default value for status_aktif if not present
        if (!$request->has('status_aktif')) {
            $request->merge(['status_aktif' => 0]);
        }

        MesinAbsensi::create($request->all());

        return redirect()->route('mesinabsensis.index')
            ->with('success', 'Mesin Absensi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(MesinAbsensi $mesinabsensi)
    {
        return view('admin.mesinabsensis.show', compact('mesinabsensi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MesinAbsensi $mesinabsensi)
    {
        return view('admin.mesinabsensis.edit', compact('mesinabsensi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MesinAbsensi $mesinabsensi)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat_ip' => 'required|string|max:255',
            'kunci_komunikasi' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'status_aktif' => 'boolean',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Set default value for status_aktif if not present
        if (!$request->has('status_aktif')) {
            $request->merge(['status_aktif' => 0]);
        }

        $mesinabsensi->update($request->all());

        return redirect()->route('mesinabsensis.index')
            ->with('success', 'Mesin Absensi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MesinAbsensi $mesinabsensi)
    {
        // Check if the machine is being used for attendance
        if ($mesinabsensi->absensisMasuk()->count() > 0 || $mesinabsensi->absensisPulang()->count() > 0) {
            return redirect()->route('mesinabsensis.index')
                ->with('error', 'Mesin Absensi tidak dapat dihapus karena sedang digunakan pada data absensi');
        }

        $mesinabsensi->delete();

        return redirect()->route('mesinabsensis.index')
            ->with('success', 'Mesin Absensi berhasil dihapus');
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(MesinAbsensi $mesinabsensi)
    {
        $mesinabsensi->status_aktif = !$mesinabsensi->status_aktif;
        $mesinabsensi->save();

        return redirect()->route('mesinabsensis.index')
            ->with('success', 'Status Mesin Absensi berhasil diperbarui');
    }

    /**
     * Test connection to the machine.
     */
    public function testConnection(MesinAbsensi $mesinabsensi)
    {
        $result = $mesinabsensi->testConnection();

        if ($result === true) {
            return redirect()->back()->with('success', 'Koneksi ke mesin absensi berhasil');
        } else {
            return redirect()->back()->with('error', 'Koneksi ke mesin absensi gagal: ' . $result);
        }
    }

    /**
     * Download attendance logs from the machine.
     */
    public function downloadLogs(MesinAbsensi $mesinabsensi)
    {
        $logs = $mesinabsensi->getAttendanceLogs();

        if (is_array($logs)) {
            return view('admin.mesinabsensis.logs', compact('mesinabsensi', 'logs'));
        } else {
            return redirect()->back()->with('error', 'Gagal mengambil log absensi: ' . $logs);
        }
    }
    public function downloadLogsRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mesin_id' => 'required|exists:mesinabsensis,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $mesinabsensi = MesinAbsensi::findOrFail($request->mesin_id);
        $logs = $mesinabsensi->getAttendanceLogs();

        // If logs retrieval failed, return with error
        if (!is_array($logs)) {
            return redirect()->back()->with('error', 'Gagal mengambil log absensi: ' . $logs);
        }

        // Filter logs based on date range
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $filteredLogs = array_filter($logs, function ($log) use ($startDate, $endDate) {
            $logDateTime = Carbon::parse($log['datetime']);
            return $logDateTime->between($startDate, $endDate);
        });

        return view('admin.mesinabsensis.download-logs-range', compact('mesinabsensi', 'filteredLogs'));
    }

    public function downloadLogsUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mesin_id' => 'required|exists:mesinabsensis,id',
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $mesinabsensi = MesinAbsensi::findOrFail($request->mesin_id);
        $logs = $mesinabsensi->getAttendanceLogs();

        // If logs retrieval failed, return with error
        if (!is_array($logs)) {
            return redirect()->back()->with('error', 'Gagal mengambil log absensi: ' . $logs);
        }

        // Filter logs for specific user
        $userId = $request->user_id;
        $filteredLogs = array_filter($logs, function ($log) use ($userId) {
            return $log['pin'] == $userId;
        });

        if (empty($filteredLogs)) {
            return redirect()->back()->with('info', 'Tidak ditemukan log absensi untuk user ID: ' . $userId);
        }

        return view('admin.mesinabsensis.logs', compact('mesinabsensi', 'filteredLogs'));
    }

    public function syncAllUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mesin_id' => 'required|exists:mesinabsensis,id',
            'confirm' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $mesinabsensi = MesinAbsensi::findOrFail($request->mesin_id);
        $result = $mesinabsensi->syncAllKaryawans();

        return redirect()->route('mesinabsensis.show', $mesinabsensi)
            ->with('info', "Sinkronisasi selesai. Berhasil: {$result['success']}, Gagal: {$result['failed']}");
    }

    /**
     * Process logs from attendance machine to create attendance records.
     */
    public function processLogs(Request $request, MesinAbsensi $mesinabsensi)
    {
        if (!$request->has('selected_logs') || !is_array($request->selected_logs) || count($request->selected_logs) === 0) {
            return redirect()->back()->with('error', 'Tidak ada log yang dipilih untuk diproses');
        }

        $processed = 0;
        $errors = 0;
        $errorMessages = [];

        foreach ($request->selected_logs as $logString) {
            try {
                list($pin, $datetime) = explode('_', $logString);
                $dateTimeObj = Carbon::parse($datetime);
                $date = $dateTimeObj->format('Y-m-d');
                $time = $dateTimeObj->format('H:i:s');

                // Find karyawan by NIK
                $karyawan = Karyawan::where('nik_karyawan', $pin)->first();
                if (!$karyawan) {
                    $errors++;
                    $errorMessages[] = "NIK tidak ditemukan: $pin";
                    continue;
                }

                // Create or update attendance record
                $absensi = Absensi::firstOrNew([
                    'karyawan_id' => $karyawan->id,
                    'tanggal' => $date
                ]);

                // If new record, set default values
                if (!$absensi->exists) {
                    $absensi->status = 'Hadir';
                    $absensi->keterlambatan = 0;
                    $absensi->pulang_awal = 0;
                }

                // Determine if this is check-in or check-out based on time
                if (!$absensi->jam_masuk || !$absensi->exists) {
                    // This is check-in
                    $absensi->jam_masuk = $time;
                    $absensi->jenis_absensi_masuk = 'Mesin';
                    $absensi->mesinabsensi_masuk_id = $mesinabsensi->id;
                } else {
                    // This is check-out
                    $absensi->jam_pulang = $time;
                    $absensi->jenis_absensi_pulang = 'Mesin';
                    $absensi->mesinabsensi_pulang_id = $mesinabsensi->id;

                    // Calculate total working hours
                    $masuk = Carbon::parse($date . ' ' . $absensi->jam_masuk);
                    $pulang = Carbon::parse($date . ' ' . $time);
                    $totalMenit = $pulang->diffInMinutes($masuk);
                    $absensi->total_jam = floor($totalMenit / 60) . ':' . str_pad($totalMenit % 60, 2, '0', STR_PAD_LEFT);
                }

                $absensi->save();
                $processed++;

            } catch (\Exception $e) {
                $errors++;
                $errorMessages[] = "Error processing log ($logString): " . $e->getMessage();
            }
        }

        $message = "Berhasil memproses $processed data absensi";
        if ($errors > 0) {
            $message .= " dengan $errors error";
            session()->flash('error_details', $errorMessages);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show form to upload user names to the machine.
     */
    public function showUploadNames(MesinAbsensi $mesinabsensi)
    {

        return view('admin.mesinabsensis.upload-names', compact('mesinabsensi'));
    }

    /**
     * Upload user names to the machine.
     */
    public function uploadNames(Request $request, MesinAbsensi $mesinabsensi)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|max:10',
            'name' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $mesinabsensi->uploadName($request->user_id, $request->name);

        return redirect()->back()->with('info', 'Hasil upload nama: ' . $result);
    }

    /**
     * Upload batch names to the attendance machine.
     */
    public function uploadNamesBatch(Request $request, MesinAbsensi $mesinabsensi)
    {
        if (!$request->has('batch_data') || empty($request->batch_data)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diupload.');
        }

        $batchData = json_decode($request->batch_data, true);

        if (!is_array($batchData) || count($batchData) === 0) {
            return redirect()->back()->with('error', 'Format data tidak valid.');
        }

        $success = 0;
        $failed = 0;
        $results = [];

        foreach ($batchData as $item) {
            $result = $mesinabsensi->uploadName($item['id'], $item['name']);

            if (strpos($result, 'successful') !== false || strpos($result, 'berhasil') !== false) {
                $success++;
            } else {
                $failed++;
            }

            $results[] = "ID: {$item['id']}, Nama: {$item['name']} - Hasil: {$result}";
        }

        // Create result message
        $resultMessage = "Upload selesai. Berhasil: $success, Gagal: $failed";

        // Store detailed results in session
        session()->flash('upload_results', $results);

        return redirect()->back()->with('info', $resultMessage);
    }

    /**
     * Get registered users from the machine.
     */
    /**
     * Get registered users from the machine.
     */
    public function getRegisteredUsers(MesinAbsensi $mesinabsensi)
    {
        try {
            // Buat koneksi ke mesin absensi
            $connect = @fsockopen($mesinabsensi->alamat_ip, "80", $errno, $errstr, 1);

            if (!$connect) {
                return response()->json([
                    'success' => false,
                    'message' => "Koneksi gagal: $errstr ($errno)"
                ]);
            }

            // Buat request SOAP untuk mendapatkan daftar pengguna
            $soap_request = "<GetAllUserInfo><ArgComKey xsi:type=\"xsd:integer\">" . $mesinabsensi->kunci_komunikasi . "</ArgComKey></GetAllUserInfo>";
            $newLine = "\r\n";

            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($connect);

            // Parse data dari response
            $users = [];
            $buffer = $this->parseDataBetween($buffer, "<GetAllUserInfoResponse>", "</GetAllUserInfoResponse>");
            $rows = explode("\r\n", $buffer);

            foreach ($rows as $row) {
                if (empty($row)) continue;

                $data = $this->parseDataBetween($row, "<Row>", "</Row>");
                if (empty($data)) continue;

                $pin = $this->parseDataBetween($data, "<PIN2>", "</PIN2>");
                $name = $this->parseDataBetween($data, "<Name>", "</Name>");

                if (!empty($pin)) {
                    $users[] = [
                        'nik' => $pin,
                        'nama' => $name
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data karyawan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Parse data between two strings
     *
     * @param string $data Original data
     * @param string $start Start delimiter
     * @param string $end End delimiter
     * @return string Parsed data
     */
    private function parseDataBetween($data, $start, $end)
    {
        $data = " " . $data;
        $startPos = strpos($data, $start);
        if ($startPos == 0) return "";

        $startPos += strlen($start);
        $endPos = strpos($data, $end, $startPos);
        if ($endPos == 0) return "";

        return substr($data, $startPos, $endPos - $startPos);
    }

    /**
     * Delete user from the machine.
     */
    /**
     * Delete user from the machine.
     */
    public function deleteUser(Request $request, MesinAbsensi $mesinabsensi)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ID User tidak valid'
            ]);
        }

        try {
            // Buat koneksi ke mesin absensi
            $connect = @fsockopen($mesinabsensi->alamat_ip, "80", $errno, $errstr, 1);

            if (!$connect) {
                return response()->json([
                    'success' => false,
                    'message' => "Koneksi gagal: $errstr ($errno)"
                ]);
            }

            // Buat request SOAP untuk menghapus pengguna
            $soap_request = "<DeleteUser><ArgComKey xsi:type=\"xsd:integer\">" . $mesinabsensi->kunci_komunikasi . "</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">" . $request->user_id . "</PIN></Arg></DeleteUser>";
            $newLine = "\r\n";

            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($connect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($connect);

            // Parse hasil dari response
            $result = $this->parseDataBetween($buffer, "<DeleteUserResponse>", "</DeleteUserResponse>");
            $information = $this->parseDataBetween($result, "<Information>", "</Information>");

            // Check if successful
            $success = strpos($information, 'successful') !== false || strpos($information, 'berhasil') !== false;

            // Update database record if user exists
            $karyawan = Karyawan::where('nik', $request->user_id)->first();
            if ($karyawan) {
                $karyawan->unregisterFromMachine($mesinabsensi->id);
            }

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Karyawan berhasil dihapus' : 'Gagal menghapus karyawan: ' . $information
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Upload batch names directly to the attendance machine.
     */
    /**
     * Upload batch names directly to the attendance machine.
     */
    public function uploadDirectBatch(Request $request, MesinAbsensi $mesinabsensi)
    {
        try {
            if (!$request->has('batch_data') || empty($request->batch_data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk diupload.'
                ]);
            }

            $batchData = json_decode($request->batch_data, true);

            if (!is_array($batchData) || count($batchData) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data tidak valid.'
                ]);
            }

            $success = 0;
            $failed = 0;
            $results = [];

            foreach ($batchData as $item) {
                // Skip admin dan NIK kosong
                if (empty($item['nik_karyawan']) || (isset($item['nama_karyawan']) && strtolower($item['nama_karyawan']) === 'admin')) {
                    continue;
                }

                $nik_karyawan = $item['nik_karyawan'];
                $nama = $item['nama_karyawan'] ?? '';

                $result = $mesinabsensi->uploadName($nik_karyawan, $nama);

                if (strpos($result, 'successful') !== false || strpos($result, 'berhasil') !== false) {
                    $success++;
                } else {
                    $failed++;
                }

                $results[] = "ID: {$nik_karyawan}, Nama: {$nama} - Hasil: {$result}";
            }

            // Store detailed results in session
            session()->flash('upload_results', $results);

            return response()->json([
                'success' => true,
                'success_count' => $success,
                'failed_count' => $failed,
                'message' => "Upload selesai. Berhasil: $success, Gagal: $failed"
            ]);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error dalam uploadDirectBatch: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }



    public function cloneUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_machine_id' => 'required|exists:mesinabsensis,id',
            'target_machine_id' => 'required|exists:mesinabsensis,id|different:source_machine_id',
            'confirm' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal. Mohon periksa kembali data yang diinputkan.');
        }

        $sourceMachine = MesinAbsensi::findOrFail($request->source_machine_id);
        $targetMachine = MesinAbsensi::findOrFail($request->target_machine_id);
        $includeFingerprint = $request->has('include_fingerprint');

        try {
            // 1. Get users from source machine
            $sourceConnect = @fsockopen($sourceMachine->alamat_ip, "80", $errno, $errstr, 1);
            if (!$sourceConnect) {
                return redirect()->back()->with('error', "Gagal terhubung ke mesin sumber: $errstr ($errno)");
            }

            // 2. Get all user info from source machine
            $soap_request = "<GetAllUserInfo><ArgComKey xsi:type=\"xsd:integer\">" . $sourceMachine->kunci_komunikasi . "</ArgComKey></GetAllUserInfo>";
            $newLine = "\r\n";

            fputs($sourceConnect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($sourceConnect, "Content-Type: text/xml" . $newLine);
            fputs($sourceConnect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($sourceConnect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($sourceConnect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($sourceConnect);

            // 3. Parse user info
            $users = [];
            $userDataRaw = $this->parseDataBetween($buffer, "<GetAllUserInfoResponse>", "</GetAllUserInfoResponse>");
            $rows = explode("\r\n", $userDataRaw);

            foreach ($rows as $row) {
                if (empty($row)) continue;

                $data = $this->parseDataBetween($row, "<Row>", "</Row>");
                if (empty($data)) continue;

                $pin = $this->parseDataBetween($data, "<PIN>", "</PIN>");
                $name = $this->parseDataBetween($data, "<Name>", "</Name>");

                // Skip admin user
                if ($name && strtolower($name) === 'admin') {
                    continue;
                }

                if (!empty($pin)) {
                    $users[] = [
                        'pin' => $pin,
                        'name' => $name
                    ];
                }
            }

            if (empty($users)) {
                return redirect()->back()->with('error', 'Tidak ada user yang ditemukan di mesin sumber.');
            }

            // 4. Process user transfer
            $success = 0;
            $failed = 0;
            $results = [];

            foreach ($users as $user) {
                // Upload user name to target machine
                $targetConnect = @fsockopen($targetMachine->alamat_ip, "80", $errno, $errstr, 1);
                if (!$targetConnect) {
                    $failed++;
                    $results[] = "User {$user['name']} (PIN: {$user['pin']}): Gagal terhubung ke mesin tujuan";
                    continue;
                }

                // Set user info (name) first
                $soap_request = "<SetUserInfo><ArgComKey Xsi:type=\"xsd:integer\">" . $targetMachine->kunci_komunikasi . "</ArgComKey><Arg><PIN>" . $user['pin'] . "</PIN><Name>" . $user['name'] . "</Name></Arg></SetUserInfo>";

                fputs($targetConnect, "POST /iWsService HTTP/1.0" . $newLine);
                fputs($targetConnect, "Content-Type: text/xml" . $newLine);
                fputs($targetConnect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
                fputs($targetConnect, $soap_request . $newLine);

                $buffer = "";
                while ($response = fgets($targetConnect, 1024)) {
                    $buffer = $buffer . $response;
                }
                fclose($targetConnect);

                $nameResult = $this->parseDataBetween($buffer, "<Information>", "</Information>");
                $nameSuccess = (strpos($nameResult, 'successful') !== false || strpos($nameResult, 'berhasil') !== false);

                // If include fingerprint is checked, transfer fingerprint templates
                if ($includeFingerprint && $nameSuccess) {
                    $fingerSuccess = $this->transferFingerprint($sourceMachine, $targetMachine, $user['pin']);

                    if ($fingerSuccess) {
                        $success++;
                        $results[] = "User {$user['name']} (PIN: {$user['pin']}): Berhasil transfer nama dan sidik jari";
                    } else {
                        $failed++;
                        $results[] = "User {$user['name']} (PIN: {$user['pin']}): Berhasil transfer nama, gagal transfer sidik jari";
                    }
                } else if ($nameSuccess) {
                    $success++;
                    $results[] = "User {$user['name']} (PIN: {$user['pin']}): Berhasil transfer nama";
                } else {
                    $failed++;
                    $results[] = "User {$user['name']} (PIN: {$user['pin']}): Gagal transfer nama";
                }
            }

            // Save results to session
            session()->flash('upload_results', $results);

            // Return result
            return redirect()->route('mesinabsensis.index')->with(
                'success',
                "Clone user selesai. Total: " . count($users) . ", Berhasil: $success, Gagal: $failed"
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Transfer fingerprint template from source machine to target machine
     */
    private function transferFingerprint($sourceMachine, $targetMachine, $pin)
    {
        try {
            // 1. Get fingerprint template from source
            $sourceConnect = @fsockopen($sourceMachine->alamat_ip, "80", $errno, $errstr, 1);
            if (!$sourceConnect) {
                return false;
            }

            $soap_request = "<GetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">" . $sourceMachine->kunci_komunikasi . "</ArgComKey><Arg><PIN>" . $pin . "</PIN></Arg></GetUserTemplate>";
            $newLine = "\r\n";

            fputs($sourceConnect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($sourceConnect, "Content-Type: text/xml" . $newLine);
            fputs($sourceConnect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($sourceConnect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($sourceConnect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($sourceConnect);

            // 2. Extract template data
            $templateData = $this->parseDataBetween($buffer, "<GetUserTemplateResponse>", "</GetUserTemplateResponse>");

            if (empty($templateData)) {
                return false;
            }

            // 3. Upload template to target machine
            $targetConnect = @fsockopen($targetMachine->alamat_ip, "80", $errno, $errstr, 1);
            if (!$targetConnect) {
                return false;
            }

            $soap_request = "<SetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">" . $targetMachine->kunci_komunikasi . "</ArgComKey><Arg>" . $templateData . "</Arg></SetUserTemplate>";

            fputs($targetConnect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($targetConnect, "Content-Type: text/xml" . $newLine);
            fputs($targetConnect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($targetConnect, $soap_request . $newLine);

            $buffer = "";
            while ($response = fgets($targetConnect, 1024)) {
                $buffer = $buffer . $response;
            }
            fclose($targetConnect);

            $result = $this->parseDataBetween($buffer, "<Information>", "</Information>");

            return (strpos($result, 'successful') !== false || strpos($result, 'berhasil') !== false);
        } catch (\Exception $e) {
            \Log::error('Error transferring fingerprint: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Auto detect IP address for the machine and update if necessary.
     */
    public function autoDetectIp(MesinAbsensi $mesinabsensi)
    {
        // Current IP segment
        $currentIpParts = explode('.', $mesinabsensi->alamat_ip);
        $originalIp = $mesinabsensi->alamat_ip;

        // If IP format is invalid, return error
        if (count($currentIpParts) !== 4) {
            return redirect()->back()->with('error', 'Format IP tidak valid: ' . $mesinabsensi->alamat_ip);
        }

        // Check current IP first with minimal timeout
        $connect = @fsockopen($mesinabsensi->alamat_ip, "80", $errno, $errstr, 0.5);
        if ($connect) {
            fclose($connect);
            return redirect()->back()->with('success', 'IP saat ini masih valid dan terhubung.');
        }

        // Get the subnet (first 3 parts of the IP)
        $subnet = $currentIpParts[0] . '.' . $currentIpParts[1] . '.' . $currentIpParts[2];
        $lastOctet = (int)$currentIpParts[3];

        // Define list of common IPs to try (limited to 15 most probable)
        $ipsToTry = [];

        // Add current IP plus/minus 1, 2
        for ($i = max(1, $lastOctet - 2); $i <= min(254, $lastOctet + 2); $i++) {
            if ($i != $lastOctet) {
                $ipsToTry[] = $i;
            }
        }

        // Add some standard IP addresses commonly used by DHCP servers
        $commonIPs = [1, 100, 101, 200, 201, 253, 254];
        foreach ($commonIPs as $ip) {
            if (!in_array($ip, $ipsToTry) && $ip != $lastOctet) {
                $ipsToTry[] = $ip;
            }
        }

        // Try each IP in our limited list
        foreach ($ipsToTry as $ip) {
            $testIp = $subnet . '.' . $ip;

            // Simple connection test with short timeout
            $connect = @fsockopen($testIp, "80", $errno, $errstr, 0.5);
            if (!$connect) continue;

            // Do a basic check to see if it responds to our request
            $soap_request = "<GetDeviceStatus><ArgComKey xsi:type=\"xsd:integer\">" . $mesinabsensi->kunci_komunikasi . "</ArgComKey></GetDeviceStatus>";
            $newLine = "\r\n";

            fputs($connect, "POST /iWsService HTTP/1.0" . $newLine);
            fputs($connect, "Content-Type: text/xml" . $newLine);
            fputs($connect, "Content-Length: " . strlen($soap_request) . $newLine . $newLine);
            fputs($connect, $soap_request . $newLine);

            // Read first chunk only, with timeout
            stream_set_timeout($connect, 0, 500000); // 0.5 second timeout
            $buffer = fgets($connect, 1024);
            fclose($connect);

            // If we got any response, assume this is our device
            if ($buffer) {
                // Update the IP
                $mesinabsensi->alamat_ip = $testIp;
                $mesinabsensi->save();

                return redirect()->route('mesinabsensis.index')
                    ->with('success', "IP berhasil diperbarui dari $originalIp menjadi $testIp");
            }
        }

        return redirect()->back()
            ->with('error', "Gagal mendeteksi mesin absensi di subnet $subnet");
    }
}

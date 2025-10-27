<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AbsensiJanuaryFebruarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all karyawan IDs
        $karyawanIds = DB::table('karyawans')->pluck('id')->toArray();

        // Get all shift IDs
        $shifts = DB::table('shifts')->get();
        if ($shifts->isEmpty()) {
            $this->command->error('No shifts found in the database. Please add shifts first.');
            return;
        }

        // Get mesin absensi IDs
        $mesinIds = DB::table('mesinabsensis')->pluck('id')->toArray();

        // If no mesin absensi found, use null
        $mesinMasukId = count($mesinIds) > 0 ? $mesinIds[0] : null;
        $mesinPulangId = count($mesinIds) > 0 ? $mesinIds[0] : null;

        // Get holiday dates
        $holidays = DB::table('hariliburs')->whereBetween('tanggal', ['2025-01-01', '2025-02-28'])
            ->pluck('tanggal')->toArray();

        // Start date: January 1, 2025
        $startDate = Carbon::create(2025, 1, 1);

        // End date: February 28, 2025
        $endDate = Carbon::create(2025, 2, 28);

        // Create a mapping of employees to their assigned shifts
        $employeeShifts = [];
        foreach ($karyawanIds as $karyawanId) {
            // Randomly assign a primary shift to each employee
            $employeeShifts[$karyawanId] = $shifts->random()->id;
        }

        // Track employee leave/permission periods
        $employeeLeaves = [];

        // Generate some random leave periods for employees
        foreach ($karyawanIds as $karyawanId) {
            // 30% chance of having a leave period
            if (rand(1, 100) <= 30) {
                $leaveStartDate = Carbon::create(2025, rand(1, 2), rand(1, 28))->format('Y-m-d');
                $leaveDuration = rand(1, 5); // 1-5 days of leave

                // Generate leave end date
                $leaveEndDate = Carbon::parse($leaveStartDate)->addDays($leaveDuration - 1)->format('Y-m-d');

                // Choose a random leave type
                $leaveTypes = ['Izin', 'Sakit', 'Cuti'];
                $leaveType = $leaveTypes[array_rand($leaveTypes)];

                // Store the leave period
                $employeeLeaves[$karyawanId][] = [
                    'start_date' => $leaveStartDate,
                    'end_date' => $leaveEndDate,
                    'type' => $leaveType
                ];

                // Create cuti_karyawans entry if it's a formal leave
                if ($leaveType == 'Cuti' || $leaveType == 'Izin') {
                    $cutiId = Str::uuid();
                    DB::table('cuti_karyawans')->insert([
                        'id' => $cutiId,
                        'id_karyawan' => $karyawanId,
                        'jenis_cuti' => $leaveType == 'Cuti' ? 'Cuti' : 'Izin',
                        'tanggal_mulai_cuti' => $leaveStartDate,
                        'tanggal_akhir_cuti' => $leaveEndDate,
                        'jumlah_hari_cuti' => $leaveDuration,
                        'cuti_disetujui' => $leaveDuration,
                        'master_cuti_id' => DB::table('mastercutis')->inRandomOrder()->first()->id,
                        'bukti' => 'dummy-bukti-' . rand(1000, 9999) . '.jpg',
                        'id_supervisor' => $karyawanIds[array_rand($karyawanIds)],
                        'status_acc' => 'Disetujui',
                        'tanggal_approval' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Update kuota cuti if it's a formal leave
                    if ($leaveType == 'Cuti') {
                        $kuotaCuti = DB::table('kuota_cuti_tahunans')
                            ->where('karyawan_id', $karyawanId)
                            ->where('tahun', 2025)
                            ->first();

                        if ($kuotaCuti) {
                            DB::table('kuota_cuti_tahunans')
                                ->where('id', $kuotaCuti->id)
                                ->update([
                                    'kuota_digunakan' => $kuotaCuti->kuota_digunakan + $leaveDuration,
                                    'kuota_sisa' => $kuotaCuti->kuota_sisa - $leaveDuration,
                                    'updated_at' => now()
                                ]);
                        }
                    }
                }
            }
        }

        // Loop through each day
        $currentDate = clone $startDate;
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->format('Y-m-d');
            $isHoliday = in_array($dateString, $holidays);
            $isSunday = $currentDate->dayOfWeek === 0; // Sunday

            // Skip if it's a holiday or Sunday
            if (!$isHoliday && !$isSunday) {
                // For each employee
                foreach ($karyawanIds as $karyawanId) {
                    // Check if employee is on leave
                    $isOnLeave = false;
                    $leaveType = null;

                    if (isset($employeeLeaves[$karyawanId])) {
                        foreach ($employeeLeaves[$karyawanId] as $leave) {
                            if ($dateString >= $leave['start_date'] && $dateString <= $leave['end_date']) {
                                $isOnLeave = true;
                                $leaveType = $leave['type'];
                                break;
                            }
                        }
                    }

                    // Create jadwal kerja for this employee on this date
                    $shiftId = $employeeShifts[$karyawanId];
                    $shift = $shifts->firstWhere('id', $shiftId);

                    $jadwalId = Str::uuid();
                    DB::table('jadwalkerjas')->insert([
                        'id' => $jadwalId,
                        'tanggal' => $dateString,
                        'shift_id' => $shiftId,
                        'karyawan_id' => $karyawanId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($isOnLeave) {
                        // Create absence record for leave
                        DB::table('absensis')->insert([
                            'karyawan_id' => $karyawanId,
                            'tanggal' => $dateString,
                            'jadwalkerja_id' => $jadwalId,
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'total_jam' => 0,
                            'keterlambatan' => 0,
                            'pulang_awal' => 0,
                            'status' => $leaveType,
                            'jenis_absensi_masuk' => 'Manual',
                            'mesinabsensi_masuk_id' => null,
                            'jenis_absensi_pulang' => 'Manual',
                            'mesinabsensi_pulang_id' => null,
                            'keterangan' => "Karyawan $leaveType",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Randomly determine attendance scenario
                        $scenario = $this->getRandomScenario();

                        // Parse shift times
                        $jamMasukShift = Carbon::parse($shift->jam_masuk);
                        $jamPulangShift = Carbon::parse($shift->jam_pulang);

                        // Determine if employee will do overtime (25% chance on regular days)
                        $hasOvertime = rand(1, 100) <= 25;
                        $overtimeHours = $hasOvertime ? rand(1, 3) : 0;

                        switch ($scenario) {
                            case 'present':
                                // Normal attendance
                                $minutesVariation = rand(-10, 5); // Slight variation
                                $jamMasuk = (clone $jamMasukShift)->addMinutes($minutesVariation);

                                $minutesVariationPulang = rand(-5, 10);
                                $jamPulang = (clone $jamPulangShift)->addMinutes($minutesVariationPulang);

                                // Add overtime if applicable
                                if ($hasOvertime) {
                                    $jamPulang->addHours($overtimeHours);
                                }

                                $keterlambatan = $minutesVariation > 0 ? $minutesVariation : 0;
                                $pulangAwal = $minutesVariationPulang < 0 && !$hasOvertime ? abs($minutesVariationPulang) : 0;

                                $status = 'Hadir';
                                break;

                            case 'late':
                                // Late arrival
                                $minutesVariation = rand(10, 60);
                                $jamMasuk = (clone $jamMasukShift)->addMinutes($minutesVariation);

                                $minutesVariationPulang = rand(-5, 10);
                                $jamPulang = (clone $jamPulangShift)->addMinutes($minutesVariationPulang);

                                // Add overtime if applicable (more likely if late, to compensate)
                                if ($hasOvertime || rand(1, 100) <= 40) {
                                    $jamPulang->addHours($overtimeHours > 0 ? $overtimeHours : rand(1, 2));
                                    $hasOvertime = true;
                                }

                                $keterlambatan = $minutesVariation;
                                $pulangAwal = $minutesVariationPulang < 0 && !$hasOvertime ? abs($minutesVariationPulang) : 0;

                                $status = 'Terlambat';
                                break;

                            case 'early_leave':
                                // Early departure
                                $minutesVariation = rand(-10, 5);
                                $jamMasuk = (clone $jamMasukShift)->addMinutes($minutesVariation);

                                $minutesVariationPulang = rand(-60, -20);
                                $jamPulang = (clone $jamPulangShift)->addMinutes($minutesVariationPulang);

                                $keterlambatan = $minutesVariation > 0 ? $minutesVariation : 0;
                                $pulangAwal = abs($minutesVariationPulang);

                                $status = 'Hadir';
                                $hasOvertime = false; // No overtime if leaving early
                                break;

                            case 'absent':
                                // Absent without notice
                                DB::table('absensis')->insert([
                                    'karyawan_id' => $karyawanId,
                                    'tanggal' => $dateString,
                                    'jadwalkerja_id' => $jadwalId,
                                    'jam_masuk' => null,
                                    'jam_pulang' => null,
                                    'total_jam' => 0,
                                    'keterlambatan' => 0,
                                    'pulang_awal' => 0,
                                    'status' => 'Izin',
                                    'jenis_absensi_masuk' => 'Manual',
                                    'mesinabsensi_masuk_id' => null,
                                    'jenis_absensi_pulang' => 'Manual',
                                    'mesinabsensi_pulang_id' => null,
                                    'keterangan' => "Tidak hadir tanpa keterangan",
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                continue 2; // Skip to next employee
                        }

                        // Calculate total hours
                        $totalJam = $jamMasuk->diffInHours($jamPulang);

                        // Create absensi record
                        $absensiId = DB::table('absensis')->insertGetId([
                            'karyawan_id' => $karyawanId,
                            'tanggal' => $dateString,
                            'jadwalkerja_id' => $jadwalId,
                            'jam_masuk' => $jamMasuk->format('H:i:s'),
                            'jam_pulang' => $jamPulang->format('H:i:s'),
                            'total_jam' => $totalJam,
                            'keterlambatan' => $keterlambatan,
                            'pulang_awal' => $pulangAwal,
                            'status' => $status,
                            'jenis_absensi_masuk' => 'Mesin',
                            'mesinabsensi_masuk_id' => $mesinMasukId,
                            'jenis_absensi_pulang' => 'Mesin',
                            'mesinabsensi_pulang_id' => $mesinPulangId,
                            'keterangan' => $hasOvertime ? "Termasuk lembur $overtimeHours jam" : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create lembur record if applicable
                        if ($hasOvertime) {
                            $this->createLemburRecord($karyawanId, $dateString, $overtimeHours, $jamPulangShift, $jamPulang, false, $absensiId);
                        }
                    }
                }
            } else if ($isHoliday || $isSunday) {
                // For holidays and Sundays
                foreach ($karyawanIds as $karyawanId) {
                    // 20% chance of having a holiday shift or overtime
                    $hasHolidayWork = rand(1, 100) <= 20;

                    if ($hasHolidayWork) {
                        $shiftId = $employeeShifts[$karyawanId];
                        $shift = $shifts->firstWhere('id', $shiftId);

                        $jadwalId = Str::uuid();
                        DB::table('jadwalkerjas')->insert([
                            'id' => $jadwalId,
                            'tanggal' => $dateString,
                            'shift_id' => $shiftId,
                            'karyawan_id' => $karyawanId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Determine if it's a full shift or just overtime hours
                        $isFullShift = rand(1, 100) <= 40; // 40% chance of working full shift on holiday

                        if ($isFullShift) {
                            // Full shift on holiday (with possible overtime)
                            $jamMasuk = Carbon::parse($shift->jam_masuk);
                            $jamPulang = Carbon::parse($shift->jam_pulang);

                            // Add possible overtime (50% chance)
                            $overtimeHours = rand(1, 100) <= 50 ? rand(1, 3) : 0;
                            if ($overtimeHours > 0) {
                                $jamPulang->addHours($overtimeHours);
                            }

                            // Calculate total hours
                            $totalJam = $jamMasuk->diffInHours($jamPulang);

                            // Create absensi record for holiday work
                            $absensiId = DB::table('absensis')->insertGetId([
                                'karyawan_id' => $karyawanId,
                                'tanggal' => $dateString,
                                'jadwalkerja_id' => $jadwalId,
                                'jam_masuk' => $jamMasuk->format('H:i:s'),
                                'jam_pulang' => $jamPulang->format('H:i:s'),
                                'total_jam' => $totalJam,
                                'keterlambatan' => 0,
                                'pulang_awal' => 0,
                                'status' => 'Hadir',
                                'jenis_absensi_masuk' => 'Mesin',
                                'mesinabsensi_masuk_id' => $mesinMasukId,
                                'jenis_absensi_pulang' => 'Mesin',
                                'mesinabsensi_pulang_id' => $mesinPulangId,
                                'keterangan' => ($isHoliday ? "Kerja di Hari Libur" : "Kerja di Hari Minggu") .
                                    ($overtimeHours > 0 ? " dengan lembur $overtimeHours jam" : ""),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // Create lembur record for holiday work (all hours count as overtime)
                            $this->createLemburRecord(
                                $karyawanId,
                                $dateString,
                                $totalJam + $overtimeHours,
                                $jamMasukShift,
                                $jamPulang,
                                true,
                                $absensiId
                            );
                        } else {
                            // Just overtime hours on holiday
                            $overtimeHours = rand(2, 5); // 2-5 hours of overtime

                            // Random start time during the day
                            $startHour = rand(8, 14);
                            $jamMasuk = Carbon::create(
                                $currentDate->year,
                                $currentDate->month,
                                $currentDate->day,
                                $startHour,
                                rand(0, 59)
                            );

                            $jamPulang = (clone $jamMasuk)->addHours($overtimeHours);

                            // Calculate total hours
                            $totalJam = $overtimeHours;

                            // Create absensi record for holiday overtime
                            $absensiId = DB::table('absensis')->insertGetId([
                                'karyawan_id' => $karyawanId,
                                'tanggal' => $dateString,
                                'jadwalkerja_id' => $jadwalId,
                                'jam_masuk' => $jamMasuk->format('H:i:s'),
                                'jam_pulang' => $jamPulang->format('H:i:s'),
                                'total_jam' => $totalJam,
                                'keterlambatan' => 0,
                                'pulang_awal' => 0,
                                'status' => 'Hadir',
                                'jenis_absensi_masuk' => 'Mesin',
                                'mesinabsensi_masuk_id' => $mesinMasukId,
                                'jenis_absensi_pulang' => 'Mesin',
                                'mesinabsensi_pulang_id' => $mesinPulangId,
                                'keterangan' => ($isHoliday ? "Lembur di Hari Libur" : "Lembur di Hari Minggu") .
                                    " selama $overtimeHours jam",
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            // Create lembur record for holiday overtime
                            $this->createLemburRecord(
                                $karyawanId,
                                $dateString,
                                $overtimeHours,
                                $jamMasuk,
                                $jamPulang,
                                true,
                                $absensiId
                            );
                        }
                    }
                }
            }

            // Move to next day
            $currentDate->addDay();
        }

        // Add overtime records for regular days
        $this->addRegularDayOvertimeRecords($startDate, $endDate, $karyawanIds, $holidays);
    }

    /**
     * Add overtime records for regular working days
     */
    private function addRegularDayOvertimeRecords($startDate, $endDate, $karyawanIds, $holidays)
    {
        // For each employee, add some random overtime on regular days
        foreach ($karyawanIds as $karyawanId) {
            // 40% chance of having overtime in this period
            if (rand(1, 100) <= 40) {
                // Generate 1-5 random overtime days
                $overtimeDaysCount = rand(1, 5);

                for ($i = 0; $i < $overtimeDaysCount; $i++) {
                    // Pick a random date in the period
                    $randomDayOffset = rand(0, $startDate->diffInDays($endDate));
                    $overtimeDate = (clone $startDate)->addDays($randomDayOffset);
                    $dateString = $overtimeDate->format('Y-m-d');

                    // Skip weekends and holidays
                    if ($overtimeDate->dayOfWeek === 0 || $overtimeDate->dayOfWeek === 6 || in_array($dateString, $holidays)) {
                        continue;
                    }

                    // Check if there's an existing attendance record
                    $existingAbsensi = DB::table('absensis')
                        ->where('karyawan_id', $karyawanId)
                        ->where('tanggal', $dateString)
                        ->first();

                    if ($existingAbsensi) {
                        // Get the jadwal kerja
                        $jadwalKerja = DB::table('jadwalkerjas')
                            ->where('id', $existingAbsensi->jadwalkerja_id)
                            ->first();

                        if ($jadwalKerja) {
                            // Get the shift
                            $shift = DB::table('shifts')
                                ->where('id', $jadwalKerja->shift_id)
                                ->first();

                            if ($shift && $existingAbsensi->status === 'Hadir') {
                                // Add overtime to this day
                                $overtimeHours = rand(1, 3);

                                // Parse existing times
                                $jamPulangOriginal = Carbon::parse($existingAbsensi->jam_pulang);
                                $jamPulangWithOvertime = (clone $jamPulangOriginal)->addHours($overtimeHours);

                                // Update the existing record
                                DB::table('absensis')
                                    ->where('id', $existingAbsensi->id)
                                    ->update([
                                        'jam_pulang' => $jamPulangWithOvertime->format('H:i:s'),
                                        'total_jam' => Carbon::parse($existingAbsensi->jam_masuk)->diffInHours($jamPulangWithOvertime),
                                        'keterangan' => "Termasuk lembur $overtimeHours jam",
                                        'updated_at' => now()
                                    ]);

                                // Create lembur record
                                $this->createLemburRecord(
                                    $karyawanId,
                                    $dateString,
                                    $overtimeHours,
                                    $jamPulangOriginal,
                                    $jamPulangWithOvertime,
                                    false,
                                    $existingAbsensi->id
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Create a lembur record
     */
    // In the createLemburRecord method, you might want to add a check for the lemburs table
    private function createLemburRecord($karyawanId, $tanggal, $jumlahJam, $jamMulai, $jamSelesai, $isHoliday, $absensiId)
    {
        // Check if lemburs table exists
        if (!Schema::hasTable('lemburs')) {
            $this->command->warn('Lemburs table not found. Skipping lembur record creation.');
            return;
        }

        // Rest of the method remains the same
        $supervisorId = DB::table('karyawans')
            ->whereNotIn('id', [$karyawanId])
            ->inRandomOrder()
            ->value('id');

        if (!$supervisorId) {
            $supervisorId = $karyawanId; // Fallback if no other employee found
        }

        // Create lembur record
        DB::table('lemburs')->insert([
            'id' => Str::uuid(),
            'karyawan_id' => $karyawanId,
            'tanggal' => $tanggal,
            'jam_mulai' => $jamMulai instanceof Carbon ? $jamMulai->format('H:i:s') : $jamMulai,
            'jam_selesai' => $jamSelesai instanceof Carbon ? $jamSelesai->format('H:i:s') : $jamSelesai,
            'jumlah_jam' => $jumlahJam,
            'keterangan' => $isHoliday ? 'Lembur di hari libur' : 'Lembur di hari kerja',
            'status' => 'Disetujui',
            'approved_by' => $supervisorId,
            'approved_at' => now(),
            'absensi_id' => $absensiId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Get a random attendance scenario
     *
     * @return string
     */
    private function getRandomScenario()
    {
        $scenarios = [
            'present' => 70,    // 70% chance of normal attendance
            'late' => 15,       // 15% chance of being late
            'early_leave' => 10, // 10% chance of leaving early
            'absent' => 5,      // 5% chance of being absent
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($scenarios as $scenario => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $scenario;
            }
        }

        return 'present'; // Default fallback
    }
}

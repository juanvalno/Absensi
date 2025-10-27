<?php

namespace App\Jobs;

use App\Models\MesinAbsensi;
use Illuminate\Bus\Queueable;
use App\Services\AttendanceService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessAttendanceScan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AttendanceService $service)
    {
        $machines = MesinAbsensi::all();
        $service->fetchMultipleMachines($machines);
    }
}

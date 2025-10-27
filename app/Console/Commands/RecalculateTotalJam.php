<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absensi;
use Carbon\Carbon;

class RecalculateTotalJam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:recalculate-total-jam {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total_jam for all attendance records based on jam_masuk and jam_pulang';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        // Get all attendance records that have both jam_masuk and jam_pulang
        $attendances = Absensi::whereNotNull('jam_masuk')
            ->whereNotNull('jam_pulang')
            ->where('jam_masuk', '!=', '')
            ->where('jam_pulang', '!=', '')
            ->get();

        $this->info("Found {$attendances->count()} attendance records to process");

        $updated = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($attendances->count());
        $progressBar->start();

        foreach ($attendances as $attendance) {
            try {
                $jamMasuk = Carbon::parse($attendance->jam_masuk);
                $jamPulang = Carbon::parse($attendance->jam_pulang);

                // Handle overnight shifts
                if ($jamPulang->lt($jamMasuk)) {
                    $jamPulang->addDay();
                }

                $diffMinutes = $jamMasuk->diffInMinutes($jamPulang);
                $newTotalJam = round($diffMinutes / 60, 2);

                // Only update if the value is different
                if ($attendance->total_jam != $newTotalJam) {
                    if (!$isDryRun) {
                        $attendance->update(['total_jam' => $newTotalJam]);
                    }
                    
                    if ($isDryRun) {
                        $this->line("\nID: {$attendance->id} - Old: {$attendance->total_jam} -> New: {$newTotalJam}");
                    }
                    
                    $updated++;
                }

            } catch (\Exception $e) {
                $this->error("\nError processing attendance ID {$attendance->id}: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        if ($isDryRun) {
            $this->info("DRY RUN completed: {$updated} records would be updated, {$errors} errors");
            $this->info("Run without --dry-run to actually update the records");
        } else {
            $this->info("Recalculation completed: {$updated} records updated, {$errors} errors");
        }

        return 0;
    }
}

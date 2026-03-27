<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\GameSession;
use Illuminate\Support\Facades\Log;

class MonitorScoundrel extends Command
{
    protected $signature = 'app:monitor-scoundrel';
    protected $description = 'Checks Scoundrel game stats and DB health';

    public function handle()
    {
        $this->info("🃏 Scoundrel Game Monitor - Status Report");

        try {
            // 1. Check Database Stats
            // We use a try-catch here in case the table doesn't exist or is empty
            $totalGames = GameSession::count() ?? 0;

            // Check for 'active' - verify if your DB uses 'active' or 'in_progress'
            $activeGames = GameSession::where('status', 'active')->count() ?? 0;

            // 2. Check Database Size (Improved for Cloud Environments)
            try {
                // Get database name dynamically from the active connection
                $dbName = DB::connection()->getDatabaseName();

                $results = DB::select('SELECT round(SUM(data_length + index_length) / 1024 / 1024, 2) AS "size"
                                       FROM information_schema.TABLES
                                       WHERE table_schema = ?', [$dbName]);

                $dbSize = $results[0]->size ?? 0;
            } catch (\Exception $e) {
                $this->warn("Could not calculate DB size: " . $e->getMessage());
                $dbSize = 0;
            }

            // 3. Display Result Table
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Games Played', $totalGames],
                    ['Currently Active', $activeGames],
                    ['DB Storage Used', $dbSize . ' MB'],
                    ['Server Time', now()->toDateTimeString()],
                ]
            );

            if ($dbSize > 500) {
                $this->error("⚠️ WARNING: Database size is high!");
            }

        } catch (\Exception $e) {
            // This ensures that if the cloud fails, you see WHY in the output
            $this->error("Command Failed: " . $e->getMessage());
            Log::error("Scoundrel Monitor Error: " . $e->getMessage());
        }
    }
}

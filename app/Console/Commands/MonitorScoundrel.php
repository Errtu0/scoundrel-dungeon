<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB; // <--- ADD THIS
use App\Models\GameSession;        // <--- ADD THIS to avoid \App\Models\ every time

class MonitorScoundrel extends Command
{
    protected $signature = 'app:monitor-scoundrel';
    protected $description = 'Checks Scoundrel game stats and DB health';

    public function handle()
    {
        $this->info("🃏 Scoundrel Game Monitor - Status Report");

        // 1. Check Database Stats
        $totalGames = GameSession::count();
        // Using 'active' or 'in_progress' based on your actual column name
        $activeGames = GameSession::where('status', 'active')->count();

        // 2. Check Database Size (MySQL logic)
        try {
            $dbName = config('database.connections.mysql.database');
            $results = DB::select('SELECT round(SUM(data_length + index_length) / 1024 / 1024, 2) AS "size"
                                   FROM information_schema.TABLES
                                   WHERE table_schema = ?', [$dbName]);
            $dbSize = $results[0]->size ?? 0;
        } catch (\Exception $e) {
            $dbSize = 0; // Fallback if not using MySQL
        }

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
    }
}

<?php

namespace App\Console\Commands;

use App\Services\OpenTdbService;
use Illuminate\Console\Command;

class SyncQuestionsCommand extends Command
{
    protected $signature = 'questions:sync-all';
    protected $description = 'Sync questions in bulk from Open Trivia Database API';

    public function handle(OpenTdbService $service)
    {
        $this->info('Starting OpenTDB Question Sync across categories...');
        $total = $service->bulkSyncQuestions(50);
        $this->info("Successfully synced {$total} questions into the database!");
    }
}

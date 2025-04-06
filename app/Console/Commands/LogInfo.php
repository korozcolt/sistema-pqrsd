<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LogInfo extends Command
{
    protected $signature = 'log:info {message}';
    protected $description = 'Log an info message';

    public function handle()
    {
        Log::info($this->argument('message'));
        $this->info('Message logged: ' . $this->argument('message'));
        return 0;
    }
}

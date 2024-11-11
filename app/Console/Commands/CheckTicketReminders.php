<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTicketReminders;
use Illuminate\Console\Command;

class CheckTicketReminders extends Command
{
    protected $signature = 'tickets:check-reminders';
    protected $description = 'Check and send ticket reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ProcessTicketReminders::dispatch();
        $this->info('Ticket reminders check queued successfully.');
    }
}

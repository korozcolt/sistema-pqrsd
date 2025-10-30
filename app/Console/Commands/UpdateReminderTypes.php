<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use App\Models\Reminder;
use Illuminate\Support\Facades\DB;

class UpdateReminderTypes extends Command
{
    protected $signature = 'reminders:update-types';
    protected $description = 'Update reminder types to match new enum values';

    public function handle()
    {
        $this->info('Starting reminder types update...');

        DB::beginTransaction();

        try {
            $updatedResolution = DB::table('reminders')
                ->where('reminder_type', 'resolution_due')
                ->update(['reminder_type' => 'day_before_resolution']);

            $updatedResponse = DB::table('reminders')
                ->where('reminder_type', 'response_due')
                ->update(['reminder_type' => 'day_before_response']);

            DB::commit();

            $this->info("Updated {$updatedResolution} resolution reminders");
            $this->info("Updated {$updatedResponse} response reminders");
            $this->info('All reminder types have been updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            $this->error('An error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

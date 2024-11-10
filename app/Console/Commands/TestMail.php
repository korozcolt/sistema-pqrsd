<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestMail extends Command
{
    protected $signature = 'mail:test {email?}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $testEmail = $this->argument('email') ?? config('site.pqrs_email');

        $this->info("Testing email configuration...");
        $this->info("Sending to: " . $testEmail);

        try {
            Mail::raw('Test email from ' . config('app.name'), function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email Configuration');

                $this->info("From: " . config('mail.from.address'));
                $this->info("From Name: " . config('mail.from.name'));
            });

            $this->info("Email sent successfully!");

            // Log the attempt
            Log::info('Test email sent', [
                'to' => $testEmail,
                'from' => config('mail.from.address'),
                'from_name' => config('mail.from.name')
            ]);
        } catch (\Exception $e) {
            $this->error("Error sending email: " . $e->getMessage());
            Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'to' => $testEmail
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BirthdaySurprise;
use App\Jobs\SendSurpriseNotification;
use App\Mail\SurpriseRevealedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notifications {--force : Force send even if surprise not revealed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification system for birthday surprises';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Email Notification System...');
        $this->newLine();

        // 1. CHECK MAIL CONFIGURATION
        $this->info('📧 Mail Configuration:');
        $this->line('Driver: ' . config('mail.default'));
        $this->line('From: ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');
        $this->newLine();

        // 2. FIND TEST SURPRISE
        $surprise = BirthdaySurprise::with(['sender', 'receiver'])->first();
        
        if (!$surprise) {
            $this->error('❌ No birthday surprises found in database.');
            $this->line('💡 Create a surprise first at /birthday-surprises/create');
            return 1;
        }

        $this->info('🎁 Found test surprise:');
        $this->line('ID: ' . $surprise->id);
        $this->line('From: ' . $surprise->sender->name . ' (' . $surprise->sender->email . ')');
        $this->line('To: ' . $surprise->receiver->name . ' (' . $surprise->receiver->email . ')');
        $this->line('Type: ' . $surprise->content_type);
        $this->line('Revealed: ' . ($surprise->is_revealed ? 'Yes' : 'No'));
        $this->newLine();

        // 3. CHECK IF SURPRISE IS REVEALED (or force)
        if (!$surprise->is_revealed && !$this->option('force')) {
            $this->error('❌ Surprise not revealed yet.');
            $this->line('💡 Use --force to test anyway, or reveal the surprise first.');
            return 1;
        }

        // 4. TEST EMAIL MAILABLE
        $this->info('📬 Testing Email Mailable...');
        try {
            $mailable = new SurpriseRevealedMail($surprise);
            Mail::send($mailable);
            $this->info('✅ Mailable test completed successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Mailable test failed: ' . $e->getMessage());
            return 1;
        }

        // 5. TEST JOB DISPATCH
        $this->info('⚡ Testing Job Dispatch...');
        try {
            SendSurpriseNotification::dispatch($surprise);
            $this->info('✅ Job dispatched successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Job dispatch failed: ' . $e->getMessage());
            return 1;
        }

        // 6. PROVIDE NEXT STEPS
        $this->newLine();
        $this->info('🎉 Email Notification System Test Completed!');
        $this->newLine();
        
        if (config('mail.default') === 'log') {
            $this->warn('📝 Since mail driver is "log", check these locations for email content:');
            $this->line('• storage/logs/laravel.log');
            $this->line('• Look for "local.INFO: " entries');
        }
        
        $this->newLine();
        $this->info('🚀 Integration Test: Reveal a surprise via browser to test full flow!');
        
        return 0;
    }
}

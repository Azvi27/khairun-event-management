<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\BirthdaySurprise;
use App\Mail\SurpriseRevealedMail;

class SendSurpriseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $surprise;
    public $tries = 3; // Retry up to 3 times if failed
    public $timeout = 30; // Timeout after 30 seconds

    /**
     * Create a new job instance.
     */
    public function __construct(BirthdaySurprise $surprise)
    {
        $this->surprise = $surprise;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. VERIFY SURPRISE IS REVEALED
            if (!$this->surprise->is_revealed) {
                Log::info("Surprise not revealed yet, skipping notification", [
                    'surprise_id' => $this->surprise->id
                ]);
                return;
            }

            // 2. CHECK IF RECEIVER HAS EMAIL
            if (!$this->surprise->receiver || !$this->surprise->receiver->email) {
                Log::warning("Receiver email not found", [
                    'surprise_id' => $this->surprise->id,
                    'receiver_id' => $this->surprise->receiver_user_id
                ]);
                return;
            }

            // 3. SEND EMAIL NOTIFICATION
            Mail::to($this->surprise->receiver->email)
                ->send(new SurpriseRevealedMail($this->surprise));

            Log::info("Surprise notification email sent successfully", [
                'surprise_id' => $this->surprise->id,
                'receiver_email' => $this->surprise->receiver->email,
                'sender_name' => $this->surprise->sender->name
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send surprise notification email", [
                'surprise_id' => $this->surprise->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw exception to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Surprise notification job failed permanently", [
            'surprise_id' => $this->surprise->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}

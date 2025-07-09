<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BirthdaySurprise;
use Carbon\Carbon;

class RevealBirthdaySurprises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surprises:reveal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reveal birthday surprises that are ready to be shown';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎁 Starting birthday surprise reveal process...');
        
        // 🔍 STEP 1: Find surprises yang siap di-reveal
        $surprisesToReveal = BirthdaySurprise::where('reveal_at', '<=', Carbon::now())
                                           ->where('is_revealed', false)
                                           ->get();
        
        // 📊 STEP 2: Check jumlah surprises
        $count = $surprisesToReveal->count();
        
        if ($count === 0) {
            $this->info('📭 No surprises ready to be revealed at this time.');
            return;
        }
        
        // 🎉 STEP 3: Process each surprise
        $this->info("🔓 Found {$count} surprise(s) ready to be revealed:");
        
        foreach ($surprisesToReveal as $surprise) {
            // Update status ke revealed
            $surprise->update(['is_revealed' => true]);
            
            // Log detail surprise
            $sender = $surprise->sender->name;
            $receiver = $surprise->receiver->name;
            $contentType = $surprise->content_type;
            $revealTime = $surprise->reveal_at->format('Y-m-d H:i:s');
            
            $this->line("   ✅ {$surprise->getContentIcon()} {$contentType} from {$sender} to {$receiver} (scheduled: {$revealTime})");
        }
        
        // 📈 STEP 4: Final summary
        $this->info("🎊 Successfully revealed {$count} birthday surprise(s)!");
        
        // 🔔 STEP 5: Optional - Log ke Laravel log untuk monitoring
        \Log::info("Birthday Surprises Auto-Reveal", [
            'revealed_count' => $count,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'surprises' => $surprisesToReveal->pluck('id')->toArray()
        ]);
        
        $this->info('✅ Birthday surprise reveal process completed!');
    }
}
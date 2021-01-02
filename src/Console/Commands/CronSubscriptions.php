<?php

namespace AlexEftimie\LaravelPayments\Console\Commands;

use AlexEftimie\LaravelPayments\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CronSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the Laravel Payments CRON once';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }

    public function endExpiredSubscriptions() {
        $collection = Subscription::where('expires_at', '>', Carbon::now())->get();

        $collection = $this->withProgressBar($collection, function ($subscription) {
            $subscription->end(Subscription::REASON_EXPIRED);
        });

    }
}

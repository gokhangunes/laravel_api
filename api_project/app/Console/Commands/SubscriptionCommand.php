<?php

namespace App\Console\Commands;

use App\Model\Subscription;
use Illuminate\Console\Command;
use App\Jobs\SubscriptionCheckJob;
use Illuminate\Support\Str;

class SubscriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        foreach (
            Subscription::where('expire_date', '<', (new \DateTime("now"))->setTimezone(new \DateTimeZone("-6"))->format('Y-m-d H:i:s'))
                ->orWhere('expire_date')
                ->cursor()
            as
            $subscription
        ) {
            SubscriptionCheckJob::dispatch([
                'receipt' => Str::random(40),
                'user_id' => $subscription->user_id
            ]);
        }

        return 1;
    }
}

<?php

namespace App\Jobs;

use App\Events\Canceled;
use App\Events\Renewed;
use App\Events\Started;
use App\Model\Device;
use App\Model\Subscription;
use App\Service\GoogleService\GoogleServie;
use App\Service\IosService\IosService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GoogleServie $googleServie, IosService $iosService)
    {
        $userId = $this->data['user_id'];
        Log::info(sprintf('User Id: %s check start', $userId));

        $device = Device::where([
            'user_id' => $userId,
        ])->first();

        if (!$device) {
            return;
        }
        dump($this->data['receipt']);
        if ($device->os === 'android') {
            $status = (bool) $googleServie->check($this->data['receipt'])['status'];
            $expireData = $googleServie->check($this->data['receipt'])['expire-date'] ?? null;

        } elseif ($device->os === 'ios') {
            $status = (bool) $iosService->check($this->data['receipt'])['status'];
            $expireData = $iosService->check($this->data['receipt'])['expire-date'] ?? null;
        } else {
            return;
        }

        $subscription = Subscription::where([
            'uid' => $device->uid,
            'app_id' => $device->application_id,
            'user_id' => $device->user_id
        ])->first();

        if (!$subscription) {
           return;
        }

        $subscription->status = $status;
        $subscription->uid = $device->uid;
        $subscription->app_id = $device->application_id;
        $subscription->expire_date = $expireData;

        $subscription->save();

        if ($expireData === null) {
            event(new Canceled($device));

            return;
        }

        event(new Renewed($device));

        Log::info(sprintf('User Id: %s check end', $userId));
    }
}

<?php

namespace App\Jobs;

use App\Model\Application;
use App\Model\Device;
use App\Service\HttpService\HttpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplicationHookJob implements ShouldQueue
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
    public function handle(HttpService $httpService)
    {
        try {
            /** @var Device $device */
            $device = $this->data['device'];
            $application = Application::where(
                'id', '=', $device->application_id
            )->first();

            $httpService->call($application->callback_url, [
                'appID' => $device->application_id,
                'deviceID' => $device->id,
                'event' => $this->data['event'],
            ]);
        } catch (\Throwable $e) {
            Log::error('repeat');
            ApplicationHookJob::dispatch($this->data);
        }
    }
}

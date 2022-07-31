<?php

namespace App\Listeners;

use App\Events\Canceled;
use App\Jobs\ApplicationHookJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class canceledWebHookListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Canceled  $event
     * @return void
     */
    public function handle(Canceled $event)
    {
        ApplicationHookJob::dispatch([
            'device' => $event->device,
            'event' => $event::NAME,
        ]);
    }
}

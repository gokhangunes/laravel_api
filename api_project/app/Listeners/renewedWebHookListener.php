<?php

namespace App\Listeners;

use App\Events\Renewed;
use App\Jobs\ApplicationHookJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class renewedWebHookListener
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
     * @param  Renewed  $event
     * @return void
     */
    public function handle(Renewed $event)
    {
        ApplicationHookJob::dispatch([
            'device' => $event->device,
            'event' => $event::NAME,
        ]);
    }
}

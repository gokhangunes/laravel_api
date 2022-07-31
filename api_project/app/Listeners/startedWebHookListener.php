<?php

namespace App\Listeners;

use App\Events\Started;
use App\Jobs\ApplicationHookJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class startedWebHookListener
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
     * @param Started $event
     *
     * @return void
     */
    public function handle(Started $event)
    {
        ApplicationHookJob::dispatch([
            'device' => $event->device,
            'event' => $event::NAME,
        ]);
    }
}

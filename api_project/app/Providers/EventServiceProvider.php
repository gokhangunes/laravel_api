<?php

namespace App\Providers;

use App\Events\Canceled;
use App\Events\Renewed;
use App\Events\Started;
use App\Jobs\ApplicationHookJob;
use App\Jobs\SubscriptionCheckJob;
use App\Listeners\canceledWebHookListener;
use App\Listeners\renewedWebHookListener;
use App\Listeners\startedWebHookListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Started::class => [
            startedWebHookListener::class
        ],
        Renewed::class => [
            renewedWebHookListener::class
        ],
        Canceled::class => [
            canceledWebHookListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->app->bind(
            sprintf('%s@handle', ApplicationHookJob::class),
            fn($job) => $job->handle(),
        );

        $this->app->bind(
            sprintf('%s@handle', SubscriptionCheckJob::class),
            fn($job) => $job->handle(),
        );
    }
}

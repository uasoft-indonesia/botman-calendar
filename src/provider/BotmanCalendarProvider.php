<?php

namespace Uasoft\BotmanCalendar;

use Illuminate\Support\ServiceProvider;

class BotmanCalendarProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../routes/api.php';
    }
}

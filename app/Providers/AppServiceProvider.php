<?php

namespace App\Providers;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Observers\GradeObserver;
use App\Observers\GradeTaskObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        GradeTask::observe(GradeTaskObserver::class);
        Grade::observe(GradeObserver::class);
    }
}
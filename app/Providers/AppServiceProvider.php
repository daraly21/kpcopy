<?php

namespace App\Providers;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Observers\GradeObserver;
use App\Observers\GradeTaskObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
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
        Model::preventLazyLoading(false);
        // DB::disableQueryLog();
        // Model::preventLazyLoading(!app()->isProduction());
        
        // Force HTTPS for production to prevent "Form is not secure" warning
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        // $this->optimizeEloquent();
    }
    
    /**
     * Optimasi tambahan untuk Eloquent
     */
    private function optimizeEloquent(): void
    {
       
        // Matikan event dispatcher jika di mode command line untuk mengurangi overhead
        if ($this->app->runningInConsole()) {
            Model::unsetEventDispatcher();
        }
    }
}
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
        DB::disableQueryLog();
        Model::preventLazyLoading(!app()->isProduction());
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        GradeTask::observe(GradeTaskObserver::class);
        Grade::observe(GradeObserver::class);
        $this->optimizeEloquent();
    }
    
    /**
     * Optimasi tambahan untuk Eloquent
     */
    private function optimizeEloquent(): void
    {
        // Batasi ukuran chunk default untuk proses batch
        // Use chunkById directly when processing large datasets instead of setting a non-existent property
        // Example: Model::query()->chunkById(100, function ($items) { ... });
        
        // Matikan event dispatcher jika di mode command line untuk mengurangi overhead
        if ($this->app->runningInConsole()) {
            Model::unsetEventDispatcher();
        }
    }
}
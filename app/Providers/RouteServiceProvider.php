<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        Route::middleware([])->group(base_path('routes/web.php'));
    }

}

<?php

use App\Http\Controllers\RegisterTenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Routes
|--------------------------------------------------------------------------
|
| These routes are only served on the central domains listed in
| config('tenancy.central_domains'). Tenant routes live in routes/tenant.php.
|
*/

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('/register', [RegisterTenantController::class, 'create'])->name('tenants.create');
        Route::post('/register', [RegisterTenantController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('tenants.store');
    });
}

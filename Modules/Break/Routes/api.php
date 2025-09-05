<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Break\Http\Controllers\Api\V11\BreakApiController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

if (!in_array(url('/'), config('tenancy.central_domains')) && config('app.mood') === 'Saas' && isModuleActive('Saas') ) {
    $middleware = [];

    if (!config('app.single_db')) {
        $middleware = [
            'api', 'cors', 'TimeZone', 'MaintenanceMode',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class
        ];
    } else {
        $middleware = [
            'api', 'cors', 'TimeZone', 'MaintenanceMode',
            'check.company.subdomain'
        ];
    }
} else {
    $middleware = ['api', 'cors', 'TimeZone', 'MaintenanceMode'];
}

Route::middleware($middleware)->group(
    function () {
        Route::group(['middleware' => ['auth:sanctum', 'cors'], 'prefix' => 'V11'], function () {
            Route::controller(BreakApiController::class)
                ->prefix('breaks')
                ->group(function () {
                    Route::get('/', 'index');
                    Route::post('qr-code-verify/{code}', 'qrCodeVerify');
                    Route::post('start/{break_type_id}', 'start');
                    Route::post('end/{break_id}', 'end');
                });
        });
    });
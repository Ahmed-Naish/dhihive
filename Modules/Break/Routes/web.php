<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

if (!in_array(url('/'), config('tenancy.central_domains')) && config('app.mood') === 'Saas' && isModuleActive('Saas')) {

    $middleware = [];

    if (!config('app.single_db')) {
        $middleware = [
            'web',
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class
        ];
    } else {
        $middleware = [
            'web',
            'check.company.subdomain'
        ];
    }

} else {
    $middleware = ['web'];
}


Route::middleware($middleware)->group(
    function () {
        Route::group(['middleware' => ['xss', 'admin', 'TimeZone'], 'prefix' => 'admin/breaks'], function () {
            Route::get('/', 'BreakController@index')->name('break.index')->middleware('PermissionCheck:break_read');
            Route::get('/table/list', 'BreakController@TableList')->name('break.table.list')->middleware('PermissionCheck:break_read');
            Route::get('/create', 'BreakController@create')->name('break.create')->middleware('PermissionCheck:break_create');
            Route::post('/store', 'BreakController@store')->name('break.store')->middleware('PermissionCheck:break_create');

            Route::group(['prefix' => 'types'], function () {
                // crud routes
                Route::get('/', 'BreakTypeController@index')->name('break.type.index')->middleware('PermissionCheck:break_type_read');
                Route::get('/table/list', 'BreakTypeController@TableList')->name('break.type.table.list')->middleware('PermissionCheck:break_type_read');
                Route::get('/create', 'BreakTypeController@create')->name('break.type.create')->middleware('PermissionCheck:break_type_create');
                Route::post('/store', 'BreakTypeController@store')->name('break.type.store')->middleware('PermissionCheck:break_type_create');
                Route::get('/edit/{id}', 'BreakTypeController@edit')->name('break.type.edit')->middleware('PermissionCheck:break_type_update');
                Route::post('/update/{id}', 'BreakTypeController@update')->name('break.type.update')->middleware('PermissionCheck:break_type_update');
                Route::get('/delete/{id}', 'BreakTypeController@destroy')->name('break.type.delete')->middleware('PermissionCheck:break_type_delete');
            });
            // Route::group(['prefix' => 'meals'], function () {
            //     Route::get('/', [MealController::class, 'meals'])->name('meals.index')->middleware('PermissionCheck:meal_read');
            //     Route::get('/search', [MealController::class, 'search'])->name('meals.search')->middleware('PermissionCheck:meal_read');
            //     Route::post('/update-meal-status', [MealController::class, 'updateStatus'])->name('meals.update-status')->middleware('PermissionCheck:meal_update');
            // });
        });
        Route::group(['prefix' => 'admin/breaks'], function () {
            Route::get('/edit/{id}', 'BreakController@edit')->name('break.edit')->middleware('PermissionCheck:break_update');
            Route::post('/update', 'BreakController@update')->name('break.update')->middleware('PermissionCheck:break_update');
            // qrcode
            Route::get('/qrcodes', 'BreakController@qrcode')->name('break.qrcode')->middleware('PermissionCheck:generate_qr_code');
            Route::post('/qrcodes/generate', 'BreakController@generate')->name('break.qrcode.generate')->middleware('PermissionCheck:generate_qr_code');
            Route::post('/qrcodes/verify', 'BreakController@verify')->name('break.qrcode.verify')->middleware('PermissionCheck:generate_qr_code');
        });
        Route::get('verifyTest/{code}', 'BreakController@verifyTest');
});

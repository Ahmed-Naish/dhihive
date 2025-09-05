<?php

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Modules\Services\Http\Controllers\ServiceBrandController;
use Modules\Services\Http\Controllers\ServiceModelController;
use Modules\Services\Http\Controllers\ModuleServiceController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Modules\Services\Http\Controllers\ServiceMachineController;
use Modules\Services\Http\Controllers\ServicePackageController;
use Modules\Services\Http\Controllers\ServiceInstitutionController;
use Modules\Services\Http\Controllers\ModuleServiceDetailController;
use Modules\Services\Http\Controllers\ServicePackageDetailController;

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


if (!in_array(url('/'), config('tenancy.central_domains')) && config('app.mood') === 'Saas' && isModuleActive('Saas') ) {
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


Route::middleware($middleware)->group(function () {
    Route::middleware(['demo.mode'])->group(function () {
        Route::prefix('services')->group(function () {
            Route::get('install', function () {
                try {
                    if (env('APP_ENV') != 'production') {
                        Artisan::call('module:migrate Services');
                        Toastr::success(_trans('response.Successfully installed'), 'success');
                        return redirect()->route('admin.dashboard');
                    }
                    abort(403);
                } catch (\Throwable $th) {
                    abort(403);
                }
            })->middleware('admin');
            Route::middleware(['FeatureCheck:services'])->group(function () {
                Route::get('/', 'ServiceController@index');
                Route::group(['prefix' => 'module-services'], function () {
                    Route::get('/', [ModuleServiceController::class, 'index'])->name('services.index')->middleware('PermissionCheck:service_read');
                    Route::get('/create', [ModuleServiceController::class, 'create'])->name('services.create')->middleware('PermissionCheck:service_create');
                    Route::post('store', [ModuleServiceController::class, 'store'])->name('services.store')->middleware('PermissionCheck:service_create');
                    Route::post('{id}/update', [ModuleServiceController::class, 'update'])->name('services.update')->middleware('PermissionCheck:service_update');
                    Route::get('create-modal', [ModuleServiceController::class, 'createModal'])->name('services.create_modal');
                    Route::get('edit-modal/{id}', [ModuleServiceController::class, 'editModal'])->name('services.edit_modal');
                    Route::post('{id}/update', [ModuleServiceController::class, 'update'])->name('services.update')->middleware('PermissionCheck:service_update');
                    Route::get('{id}/edit', [ModuleServiceController::class, 'edit'])->name('services.edit')->middleware('PermissionCheck:service_delete');
                    Route::get('delete/{moduleService}', [ModuleServiceController::class, 'delete'])->name('services.delete')->middleware('PermissionCheck:service_delete');
                    Route::post('delete-data', [ModuleServiceController::class, 'deleteData'])->name('services.delete_data')->middleware('PermissionCheck:service_delete');
                    Route::post('status-change', [ModuleServiceController::class, 'statusUpdate'])->name('services.statusUpdate')->middleware('PermissionCheck:service_update');
                    Route::post('/getModels', [ModuleServiceController::class, 'getModels'])->name('services.getBrandsAndModels');
                });

                Route::group(['prefix' => 'module-service-details'], function () {
                    Route::get('/view/{id}', [ModuleServiceDetailController::class, 'view'])->name('serviceDetails.view');
                    Route::get('/', [ModuleServiceDetailController::class, 'index'])->name('serviceDetails.index');
                    Route::get('/create', [ModuleServiceDetailController::class, 'create'])->name('serviceDetails.create');
                    Route::post('store', [ModuleServiceDetailController::class, 'store'])->name('serviceDetails.store');
                    Route::post('{id}/update', [ModuleServiceDetailController::class, 'update'])->name('serviceDetails.update');
                    Route::get('create-modal/{id}', [ModuleServiceDetailController::class, 'createModal'])->name('serviceDetails.create_modal');
                    Route::post('new-store/{id}', [ModuleServiceDetailController::class, 'newStore'])->name('serviceDetails.newStore');
                    Route::get('edit-modal/{id}', [ModuleServiceDetailController::class, 'editModal'])->name('serviceDetails.edit_modal');
                    Route::post('{id}/update', [ModuleServiceDetailController::class, 'update'])->name('serviceDetails.update');
                    Route::get('{id}/edit', [ModuleServiceDetailController::class, 'edit'])->name('serviceDetails.edit');
                    Route::get('delete/{moduleService}', [ModuleServiceDetailController::class, 'delete'])->name('serviceDetails.delete');
                    Route::post('delete-data', [ModuleServiceDetailController::class, 'deleteData'])->name('serviceDetails.delete_data');
                    Route::post('status-change', [ModuleServiceDetailController::class, 'statusUpdate'])->name('serviceDetails.statusUpdate');
                    Route::post('/getModels', [ModuleServiceDetailController::class, 'getModels'])->name('serviceDetails.getBrandsAndModels');
                });

                Route::group(['prefix' => 'models'], function () {
                    Route::get('/', [ServiceModelController::class, 'index'])->name('models.index')->middleware('PermissionCheck:model_read');
                    Route::get('/create', [ServiceModelController::class, 'create'])->name('models.create')->middleware('PermissionCheck:model_create');
                    Route::post('store', [ServiceModelController::class, 'store'])->name('models.store')->middleware('PermissionCheck:model_create');
                    Route::post('{id}/update', [ServiceModelController::class, 'update'])->name('models.update')->middleware('PermissionCheck:model_update');
                    Route::get('create-modal', [ServiceModelController::class, 'createModal'])->name('models.create_modal')->middleware('PermissionCheck:model_create');
                    Route::get('edit-modal/{id}', [ServiceModelController::class, 'editModal'])->name('models.edit_modal')->middleware('PermissionCheck:model_update');
                    Route::post('{id}/update', [ServiceModelController::class, 'update'])->name('models.update')->middleware('PermissionCheck:model_update');
                    Route::get('{id}/edit', [ServiceModelController::class, 'edit'])->name('models.edit')->middleware('PermissionCheck:model_update');
                    Route::get('delete/{serviceModel}', [ServiceModelController::class, 'delete'])->name('models.delete')->middleware('PermissionCheck:model_delete');
                    Route::post('delete-data', [ServiceModelController::class, 'deleteData'])->name('models.delete_data')->middleware('PermissionCheck:model_delete');
                    Route::post('status-change', [ServiceModelController::class, 'statusUpdate'])->name('models.statusUpdate')->middleware('PermissionCheck:model_update');
                });
                Route::group(['prefix' => 'brands'], function () {
                    Route::get('/', [ServiceBrandController::class, 'index'])->name('brands.index')->middleware('PermissionCheck:brand_read');
                    Route::get('/create', [ServiceBrandController::class, 'create'])->name('brands.create')->middleware('PermissionCheck:brand_create');
                    Route::post('store', [ServiceBrandController::class, 'store'])->name('brands.store')->middleware('PermissionCheck:brand_create');
                    Route::post('{id}/update', [ServiceBrandController::class, 'update'])->name('brands.update')->middleware('PermissionCheck:brand_update');
                    Route::get('create-modal', [ServiceBrandController::class, 'createModal'])->name('brands.create_modal')->middleware('PermissionCheck:brand_create');
                    Route::get('edit-modal/{id}', [ServiceBrandController::class, 'editModal'])->name('brands.edit_modal')->middleware('PermissionCheck:brand_update');
                    Route::post('{id}/update', [ServiceBrandController::class, 'update'])->name('brands.update')->middleware('PermissionCheck:brand_update');
                    Route::get('{id}/edit', [ServiceBrandController::class, 'edit'])->name('brands.edit')->middleware('PermissionCheck:brand_update');
                    Route::get('delete/{serviceBrand}', [ServiceBrandController::class, 'delete'])->name('brands.delete')->middleware('PermissionCheck:brand_delete');
                    Route::post('delete-data', [ServiceBrandController::class, 'deleteData'])->name('brands.delete_data')->middleware('PermissionCheck:brand_delete');
                    Route::post('status-change', [ServiceBrandController::class, 'statusUpdate'])->name('brands.statusUpdate')->middleware('PermissionCheck:brand_update');
                });
                Route::group(['prefix' => 'packages'], function () {
                    Route::get('/', [ServicePackageController::class, 'index'])->name('packages.index')->middleware('PermissionCheck:package_read');
                    Route::get('/create', [ServicePackageController::class, 'create'])->name('packages.create')->middleware('PermissionCheck:package_create');
                    Route::post('store', [ServicePackageController::class, 'store'])->name('packages.store')->middleware('PermissionCheck:package_create');
                    Route::post('{id}/update', [ServicePackageController::class, 'update'])->name('packages.update')->middleware('PermissionCheck:package_update');
                    Route::get('create-modal', [ServicePackageController::class, 'createModal'])->name('packages.create_modal')->middleware('PermissionCheck:package_create');
                    Route::get('edit-modal/{id}', [ServicePackageController::class, 'editModal'])->name('packages.edit_modal')->middleware('PermissionCheck:package_update');
                    Route::post('{id}/update', [ServicePackageController::class, 'update'])->name('packages.update')->middleware('PermissionCheck:package_update');
                    Route::get('{id}/edit', [ServicePackageController::class, 'edit'])->name('packages.edit')->middleware('PermissionCheck:package_update');
                    Route::get('delete/{servicePackage}', [ServicePackageController::class, 'delete'])->name('packages.delete')->middleware('PermissionCheck:package_delete');
                    Route::post('delete-data', [ServicePackageController::class, 'deleteData'])->name('packages.delete_data')->middleware('PermissionCheck:package_delete');
                    Route::post('status-change', [ServicePackageController::class, 'statusUpdate'])->name('packages.statusUpdate')->middleware('PermissionCheck:package_update');
                    Route::post('/getBrandsAndModels', [ServicePackageController::class, 'getBrandsAndModels'])->name('packages.getBrandsAndModels');

                });
                Route::group(['prefix' => 'package-details'], function () {
                    Route::get('/view/{id}', [ServicePackageDetailController::class, 'singleView'])->name('packageDetails.singleView');
                    Route::get('/', [ServicePackageDetailController::class, 'index'])->name('packageDetails.index');
                    Route::get('/create', [ServicePackageDetailController::class, 'create'])->name('packageDetails.create');
                    Route::post('store', [ServicePackageDetailController::class, 'store'])->name('packageDetails.store');
                    Route::post('new-store/{id}', [ServicePackageDetailController::class, 'newStore'])->name('packageDetails.newStore');
                    Route::post('{id}/update', [ServicePackageDetailController::class, 'update'])->name('packageDetails.update');
                    Route::get('create-modal/{id}', [ServicePackageDetailController::class, 'createModal'])->name('packageDetails.create_modal');
                    Route::get('edit-modal/{id}', [ServicePackageDetailController::class, 'editModal'])->name('packageDetails.edit_modal');
                    Route::post('{id}/update', [ServicePackageDetailController::class, 'update'])->name('packageDetails.update');
                    Route::get('{id}/edit', [ServicePackageDetailController::class, 'edit'])->name('packageDetails.edit');
                    Route::get('delete/{servicePackageDetails}', [ServicePackageDetailController::class, 'delete'])->name('packageDetails.delete');
                    Route::post('delete-data', [ServicePackageDetailController::class, 'deleteData'])->name('packageDetails.delete_data');
                    Route::post('status-change', [ServicePackageDetailController::class, 'statusUpdate'])->name('packageDetails.statusUpdate');
                    Route::post('/getModels', [ServicePackageDetailController::class, 'getBrandsAndModels'])->name('packageDetails.getBrandsAndModels');
                });
                Route::group(['prefix' => 'machines'], function () {
                    Route::get('/', [ServiceMachineController::class, 'index'])->name('machines.index')->middleware('PermissionCheck:machine_read');
                    Route::get('/create', [ServiceMachineController::class, 'create'])->name('machines.create')->middleware('PermissionCheck:machine_create');
                    Route::post('store', [ServiceMachineController::class, 'store'])->name('machines.store')->middleware('PermissionCheck:machine_create');
                    Route::post('{id}/update', [ServiceMachineController::class, 'update'])->name('machines.update')->middleware('PermissionCheck:machine_update');
                    Route::get('create-modal', [ServiceMachineController::class, 'createModal'])->name('machines.create_modal')->middleware('PermissionCheck:machine_create');
                    Route::get('edit-modal/{id}', [ServiceMachineController::class, 'editModal'])->name('machines.edit_modal')->middleware('PermissionCheck:machine_update');
                    Route::post('{id}/update', [ServiceMachineController::class, 'update'])->name('machines.update')->middleware('PermissionCheck:machine_update');
                    Route::get('{id}/edit', [ServiceMachineController::class, 'edit'])->name('machines.edit')->middleware('PermissionCheck:machine_update');
                    Route::get('delete/{serviceMachine}', [ServiceMachineController::class, 'delete'])->name('machines.delete')->middleware('PermissionCheck:machine_delete');
                    Route::post('delete-data', [ServiceMachineController::class, 'deleteData'])->name('machines.delete_data')->middleware('PermissionCheck:machine_delete');
                    Route::post('status-change', [ServiceMachineController::class, 'statusUpdate'])->name('machines.statusUpdate')->middleware('PermissionCheck:machine_update');
                });
                Route::group(['prefix' => 'institutions'], function () {
                    Route::get('/', [ServiceInstitutionController::class, 'index'])->name('institutions.index')->middleware('PermissionCheck:institution_read');
                    Route::get('/create', [ServiceInstitutionController::class, 'create'])->name('institutions.create')->middleware('PermissionCheck:institution_create');
                    Route::post('store', [ServiceInstitutionController::class, 'store'])->name('institutions.store')->middleware('PermissionCheck:institution_create');
                    Route::post('{id}/update', [ServiceInstitutionController::class, 'update'])->name('institutions.update')->middleware('PermissionCheck:institution_update');
                    Route::get('create-modal', [ServiceInstitutionController::class, 'createModal'])->name('institutions.create_modal')->middleware('PermissionCheck:institution_create');
                    Route::get('edit-modal/{id}', [ServiceInstitutionController::class, 'editModal'])->name('institutions.edit_modal')->middleware('PermissionCheck:institution_update');
                    Route::post('{id}/update', [ServiceInstitutionController::class, 'update'])->name('institutions.update')->middleware('PermissionCheck:institution_update');
                    Route::get('{id}/edit', [ServiceInstitutionController::class, 'edit'])->name('institutions.edit')->middleware('PermissionCheck:institution_update');
                    Route::get('delete/{serviceInstitution}', [ServiceInstitutionController::class, 'delete'])->name('institutions.delete')->middleware('PermissionCheck:institution_delete');
                    Route::post('delete-data', [ServiceInstitutionController::class, 'deleteData'])->name('institutions.delete_data')->middleware('PermissionCheck:institution_delete');
                    Route::post('status-change', [ServiceInstitutionController::class, 'statusUpdate'])->name('institutions.statusUpdate')->middleware('PermissionCheck:institution_update');
                });
            });
        });
    });
});
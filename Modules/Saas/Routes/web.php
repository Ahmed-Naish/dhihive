<?php

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

use Illuminate\Support\Facades\Route;
use Modules\Saas\Http\Controllers\Backend\ModuleCategoryController;
use Modules\Saas\Http\Controllers\Backend\ModuleController;
use Modules\Saas\Http\Controllers\Backend\ModuleTermController;
use Modules\Saas\Http\Controllers\Backend\ModuleValueController;
use Modules\Saas\Http\Controllers\Backend\PackageController;
use Modules\Saas\Http\Controllers\Backend\PackagePlanController;
use Modules\Saas\Http\Controllers\Backend\PaymentMethodController;
use Modules\Saas\Http\Controllers\Backend\PricingPlanController;
use Modules\Saas\Http\Controllers\Backend\ProductController;
use Modules\Saas\Http\Controllers\Backend\ProductPlanController;
use Modules\Saas\Http\Controllers\Backend\PurchasedPackageController;
use Modules\Saas\Http\Controllers\Backend\SaasCurrencyController;
use Modules\Saas\Http\Controllers\SaasCompanyController;

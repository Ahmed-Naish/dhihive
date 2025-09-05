<?php

use App\Http\Controllers\coreApp\Setting\LanguageSettingController;
use App\Models\Company\Company;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\HomeController;
use Modules\Saas\Http\Controllers\CmsController;
use Modules\Saas\Http\Controllers\ReportController;
use Modules\Saas\Http\Controllers\CheckoutController;
use App\Http\Controllers\Frontend\Auth\LoginController;
use Modules\Saas\Http\Controllers\PricingPlanController;
use Modules\Saas\Http\Controllers\SaasCompanyController;
use Modules\Saas\Http\Controllers\SaasFrontendController;
use Modules\Saas\Http\Controllers\SubscriptionController;
use Modules\Saas\Http\Controllers\EmailTemplateController;
use Modules\Saas\Http\Controllers\Backend\ModuleController;
use Modules\Saas\Http\Controllers\Backend\PackageController;
use Modules\Saas\Http\Controllers\Backend\ProductController;
use Modules\Saas\Http\Controllers\Backend\DashboardController;
use Modules\Saas\Http\Controllers\Backend\ModuleTermController;
use Modules\Saas\Http\Controllers\DeactivatedCompanyController;
use Modules\Saas\Http\Controllers\Backend\ModuleValueController;
use Modules\Saas\Http\Controllers\Backend\PackagePlanController;
use Modules\Saas\Http\Controllers\Backend\ProductPlanController;
use Modules\Saas\Http\Controllers\Backend\SaasCurrencyController;
use Modules\Saas\Http\Controllers\Backend\PaymentMethodController;
use Modules\Saas\Http\Controllers\Backend\ModuleCategoryController;
use Modules\Saas\Http\Controllers\Backend\PurchasedPackageController;
use Modules\Saas\Http\Controllers\ContactMessageController;
use Modules\Saas\Http\Controllers\Single\SingleCompanyPricingPlanConroller;
use Modules\Saas\Http\Controllers\Single\SingleCompanySubscriptionController;
use Modules\Saas\Http\Controllers\SubscriberController;

if (config('app.mood') === "Saas" && isModuleActive("Saas")) {


    
    Route::group(['middleware' => ['admin'], 'prefix' => 'admin/saas/single-company', 'as' => 'single-company.'], function () {
        Route::get('subscriptions', [SingleCompanySubscriptionController::class, 'index'])->name('subscriptions.index')->middleware('PermissionCheck:subscription_read');
        // Route::get('subscriptions/{id}/invoice', [SingleCompanySubscriptionController::class, 'invoice'])->name('subscriptions.invoice')->middleware('PermissionCheck:subscription_invoice');
        Route::get('subscriptions/{id}/invoice', [SingleCompanySubscriptionController::class, 'invoice'])->name('subscriptions.invoice');
        Route::get('deactivated', DeactivatedCompanyController::class)->name('deactivated');

        Route::get('upgrade-plan', [SingleCompanyPricingPlanConroller::class, 'upgradePlan'])->name('upgrade-plan.index')->middleware('PermissionCheck:subscription_upgrade');
        Route::get('upgrade-plan-modal/{pricing_plan_price_id}', [SingleCompanyPricingPlanConroller::class, 'upgradePlanModal'])->name('upgrade-plan.modal');
        Route::post('upgrade-plan-store', [SingleCompanyPricingPlanConroller::class, 'upgradePlanStore'])->name('upgrade-plan.store');
    });


    if(in_array(url('/'), config('tenancy.central_domains'))){

        Route::middleware(['frontend'])->group(function () {
            Route::get('/', [SaasFrontendController::class, 'homePage'])->name('saas.homePage');
            Route::get('/features', [SaasFrontendController::class, 'featurePage'])->name('saas.featurePage');
            Route::get('/pricing', [SaasFrontendController::class, 'pricingPage'])->name('saas.pricingPage');
            Route::get('/contact', [SaasFrontendController::class, 'contactPage'])->name('saas.contactPage');
            Route::post('/store-contact', [SaasFrontendController::class, 'storeContact'])->name('saas.storeContact');
            Route::post('/module', [SaasFrontendController::class, 'getTabContent'])->name('saas.getTabContent');
            Route::any('/solutions', [SaasFrontendController::class, 'solutions'])->name('saas.solutions');
            Route::get('/pages/content/{slug}', [SaasFrontendController::class, 'content'])->name('saascontent');
            Route::get('/checkout', [CheckoutController::class, 'checkoutPage'])->name('saas.checkoutPage');
            Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('saas.checkout');
            Route::get('/invoice/{invoice_id}', [CheckoutController::class, 'invoice'])->name('saas.invoice');
            Route::post('/check-company-name', [CheckoutController::class, 'checkCompanyName'])->name('saas.check-company-name');
            Route::post('/check-subdomain', [CheckoutController::class, 'checkSubdomain'])->name('saas.check-subdomain');
            Route::post('/check-email', [CheckoutController::class, 'checkEmail'])->name('saas.check-email');
            Route::post('/check-phone', [CheckoutController::class, 'checkPhone'])->name('saas.check-phone');
            Route::post('/store-subscriber', [SaasFrontendController::class, 'storeSubscriber'])->name('saas.storeSubscriber');
        
            Route::post('/check-is-unique-value-by-company-column', [CheckoutController::class, 'checkIsUniqueValueByCompanyColumn'])->name('saas.check-is-unique-value-by-company-column');
        
            Route::post('/ajax-frontend-lang-change', [LanguageSettingController::class, 'ajaxFrontendLangChange'])->name('language.ajaxFrontendLangChange');
        });
    

        Route::middleware(['demo.mode'])->group(function () {
            Route::group(['middleware' => ['xss', 'admin', 'TimeZone'], 'prefix' => 'admin/saas'], function () {

                Route::any('/', 'SaasController@index');

                Route::get('/dashboard', [DashboardController::class, 'index'])->name('saas.dashboard');

                Route::any('/company-change', 'SaasController@ajaxCompanyChange')->name('company.ajaxCompanyChange');

                Route::any('companies', [SaasCompanyController::class, 'index'])->name('saas.company.list');
                Route::any('companies/trash', [SaasCompanyController::class, 'trashList'])->name('saas.company.trash.list');
                // Route::any('companies/create', [SaasCompanyController::class, 'createModal'])->name('saas.company.create');
                Route::any('companies/create', [SaasCompanyController::class, 'create'])->name('saas.company.create');
                Route::post('companies/store', [SaasCompanyController::class, 'store'])->name('saas.company.store');
                Route::any('companies/{company}/edit', [SaasCompanyController::class, 'edit'])->name('saas.company.edit');
                Route::post('companies/update/{company_id}', [SaasCompanyController::class, 'update'])->name('saas.company.update');

                Route::any('companies/{company}/domain/edit', [SaasCompanyController::class, 'domainEdit'])->name('saas.company.domain.edit');
                Route::post('companies/{company}/domain/edit', [SaasCompanyController::class, 'domainUpdate'])->name('saas.company.domain.update');

                Route::get('companies/delete/{company_id}', [SaasCompanyController::class, 'delete'])->name('saas.company.delete');
                Route::get('companies/restore/{company_id}', [SaasCompanyController::class, 'restore'])->name('saas.company.restore');
                Route::get('companies/permanent-delete/{company_id}', [SaasCompanyController::class, 'permanentDelete'])->name('saas.company.permanent.delete');

                Route::post('status-change', [SaasCompanyController::class, 'statusUpdate'])->name('saas.company.statusUpdate');

                Route::any('subscriptions/list', [SubscriptionController::class, 'list'])->name('saas.subscriptions.list');
                Route::post('subscriptions/{id}/approve', [SubscriptionController::class, 'approve'])->name('saas.subscriptions.approve');
                Route::post('subscriptions/{id}/reject', [SubscriptionController::class, 'reject'])->name('saas.subscriptions.reject');

                Route::get('fetch-processing-subscriptions', [SubscriptionController::class, 'fetchProcessingSubscriptions'])->name('saas.subscriptions.fetch-processing-subscriptions');


                Route::any('price-plans', [PricingPlanController::class, 'index'])->name('saas.pricePlan.list');
                Route::any('price-plans/create', [PricingPlanController::class, 'create'])->name('saas.pricePlan.create');
                Route::post('price-plans/store', [PricingPlanController::class, 'store'])->name('saas.pricePlan.store');
                Route::any('price-plans/{id}/edit', [PricingPlanController::class, 'edit'])->name('saas.pricePlan.edit');
                Route::post('price-plans/update/{id}', [PricingPlanController::class, 'update'])->name('saas.pricePlan.update');
                Route::any('price-plans/delete/{id}', [PricingPlanController::class, 'delete'])->name('saas.pricePlan.delete');

                Route::post('price-plans/status-change', [PricingPlanController::class, 'statusUpdate'])->name('saas.pricePlan.statusUpdate');

                Route::group(['prefix' => 'currencies'], function () {
                    Route::any('/', [SaasCurrencyController::class, 'index'])->name('saas.subscription_currencies.index');
                    Route::get('{id}/edit', [SaasCurrencyController::class, 'edit'])->name('saas.subscription_currencies.edit');
                    Route::get('delete/{currency}', [SaasCurrencyController::class, 'delete'])->name('saas.subscription_currencies.delete');
                    Route::get('create', [SaasCurrencyController::class, 'create'])->name('saas.subscription_currencies.create');
                    Route::post('store', [SaasCurrencyController::class, 'store'])->name('saas.subscription_currencies.store');
                    Route::get('create-modal', [SaasCurrencyController::class, 'createModal'])->name('saas.subscription_currencies.create_modal');
                    Route::get('edit-modal/{id}', [SaasCurrencyController::class, 'editModal'])->name('saas.subscription_currencies.edit_modal');
                    Route::post('{id}/update', [SaasCurrencyController::class, 'update'])->name('saas.subscription_currencies.update');
                    Route::post('delete-data', [SaasCurrencyController::class, 'deleteData'])->name('saas.subscription_currencies.delete_data');
                    Route::post('status-change', [SaasCurrencyController::class, 'statusUpdate'])->name('saas.subscription_currencies.statusUpdate');
                });
                // subscription_payment_methods
                Route::group(['prefix' => 'payment-methods'], function () {
                    Route::get('/', [PaymentMethodController::class, 'index'])->name('saas.subscription_payment_methods.index');
                    Route::get('{id}/edit', [PaymentMethodController::class, 'edit'])->name('saas.subscription_payment_methods.edit');
                    Route::get('delete/{paymentMethod}', [PaymentMethodController::class, 'delete'])->name('saas.subscription_payment_methods.delete');
                    Route::get('create', [PaymentMethodController::class, 'create'])->name('saas.subscription_payment_methods.create');
                    Route::post('store', [PaymentMethodController::class, 'store'])->name('saas.subscription_payment_methods.store');
                    Route::get('create-modal', [PaymentMethodController::class, 'createModal'])->name('saas.subscription_payment_methods.create_modal');
                    Route::get('edit-modal/{id}', [PaymentMethodController::class, 'editModal'])->name('saas.subscription_payment_methods.edit_modal');
                    Route::post('{id}/update', [PaymentMethodController::class, 'update'])->name('saas.subscription_payment_methods.update');
                    Route::post('delete-data', [PaymentMethodController::class, 'deleteData'])->name('saas.subscription_payment_methods.delete_data');
                    Route::post('status-change', [PaymentMethodController::class, 'statusUpdate'])->name('saas.subscription_payment_methods.statusUpdate');
                });

                // subscription_products
                Route::group(['prefix' => 'products'], function () {
                    Route::get('/', [ProductController::class, 'index'])->name('saas.subscription_products.index');
                    Route::get('edit/{id}', [ProductController::class, 'edit'])->name('saas.subscription_products.edit');
                    Route::get('delete/{product}', [ProductController::class, 'delete'])->name('saas.subscription_products.delete');
                    Route::get('create', [ProductController::class, 'create'])->name('saas.subscription_products.create');
                    Route::post('store', [ProductController::class, 'store'])->name('saas.subscription_products.store');
                    Route::post('update', [ProductController::class, 'update'])->name('saas.subscription_products.update');
                    Route::post('update/{id}', [ProductController::class, 'update'])->name('saas.subscription_products.update');
                    Route::post('delete-data', [ProductController::class, 'deleteData'])->name('saas.subscription_products.delete_data');
                    Route::post('status-change', [ProductController::class, 'statusUpdate'])->name('saas.subscription_products.statusUpdate');
                });

                // subscription_module_categories
                Route::group(['prefix' => 'module-categories'], function () {
                    Route::get('/', [ModuleCategoryController::class, 'index'])->name('saas.subscription_module_categories.index');
                    Route::get('edit/{id}', [ModuleCategoryController::class, 'edit'])->name('saas.subscription_module_categories.edit');
                    Route::get('delete/{moduleCategory}', [ModuleCategoryController::class, 'delete'])->name('saas.subscription_module_categories.delete');
                    Route::get('create', [ModuleCategoryController::class, 'create'])->name('saas.subscription_module_categories.create');
                    Route::post('store', [ModuleCategoryController::class, 'store'])->name('saas.subscription_module_categories.store');
                    Route::post('update/{id}', [ModuleCategoryController::class, 'update'])->name('saas.subscription_module_categories.update');
                    Route::get('delete/{id}', [ModuleCategoryController::class, 'delete'])->name('saas.subscription_module_categories.delete');
                    Route::post('delete-data', [ModuleCategoryController::class, 'deleteData'])->name('saas.subscription_module_categories.delete_data');
                    Route::post('status-change', [ModuleCategoryController::class, 'statusUpdate'])->name('saas.subscription_module_categories.statusUpdate');
                });

                // subscription_module_terms
                Route::group(['prefix' => 'module-terms'], function () {
                    Route::get('/', [ModuleTermController::class, 'index'])->name('saas.subscription_module_terms.index');
                    Route::get('{id}/edit', [ModuleTermController::class, 'edit'])->name('saas.subscription_module_terms.edit');
                    Route::get('delete/{moduleTerm}', [ModuleTermController::class, 'delete'])->name('saas.subscription_module_terms.delete');
                    Route::get('create', [ModuleTermController::class, 'create'])->name('saas.subscription_module_terms.create');
                    Route::post('store', [ModuleTermController::class, 'store'])->name('saas.subscription_module_terms.store');
                    Route::get('create-modal', [ModuleTermController::class, 'createModal'])->name('saas.subscription_module_terms.create_modal');
                    Route::get('edit-modal/{id}', [ModuleTermController::class, 'editModal'])->name('saas.subscription_module_terms.edit_modal');
                    Route::post('{id}/update', [ModuleTermController::class, 'update'])->name('saas.subscription_module_terms.update');
                    Route::post('delete-data', [ModuleTermController::class, 'deleteData'])->name('saas.subscription_module_terms.delete_data');
                    Route::post('status-change', [ModuleTermController::class, 'statusUpdate'])->name('saas.subscription_module_terms.statusUpdate');
                });
                // subscription_module_values
                Route::group(['prefix' => 'module-values'], function () {
                    Route::get('/', [ModuleValueController::class, 'index'])->name('saas.subscription_module_values.index');
                    Route::get('{id}/edit', [ModuleValueController::class, 'edit'])->name('saas.subscription_module_values.edit');
                    Route::get('delete/{moduleValue}', [ModuleValueController::class, 'delete'])->name('saas.subscription_module_values.delete');
                    Route::get('create', [ModuleValueController::class, 'create'])->name('saas.subscription_module_values.create');
                    Route::post('store', [ModuleValueController::class, 'store'])->name('saas.subscription_module_values.store');
                    Route::get('create-modal', [ModuleValueController::class, 'createModal'])->name('saas.subscription_module_values.create_modal');
                    Route::get('edit-modal/{id}', [ModuleValueController::class, 'editModal'])->name('saas.subscription_module_values.edit_modal');
                    Route::post('{id}/update', [ModuleValueController::class, 'update'])->name('saas.subscription_module_values.update');
                    Route::post('delete-data', [ModuleValueController::class, 'deleteData'])->name('saas.subscription_module_values.delete_data');
                    Route::post('status-change', [ModuleValueController::class, 'statusUpdate'])->name('saas.subscription_module_values.statusUpdate');
                });

                // subscription_modules
                Route::group(['prefix' => 'modules'], function () {
                    Route::get('/', [ModuleController::class, 'index'])->name('saas.subscription_modules.index');
                    Route::get('edit/{id}', [ModuleController::class, 'edit'])->name('saas.subscription_modules.edit');
                    Route::get('assign/{id}', [ModuleController::class, 'assign'])->name('saas.subscription_modules.assign');
                    Route::post('assign-store/{id}', [ModuleController::class, 'assignStore'])->name('saas.subscription_modules.assignStore');
                    Route::get('delete/{module}', [ModuleController::class, 'delete'])->name('saas.subscription_modules.delete');
                    Route::get('create', [ModuleController::class, 'create'])->name('saas.subscription_modules.create');
                    Route::post('store', [ModuleController::class, 'store'])->name('saas.subscription_modules.store');
                    Route::post('update/{id}', [ModuleController::class, 'update'])->name('saas.subscription_modules.update');
                    Route::post('delete-data', [ModuleController::class, 'deleteData'])->name('saas.subscription_modules.delete_data');
                    Route::post('status-change', [ModuleController::class, 'statusUpdate'])->name('saas.subscription_modules.statusUpdate');
                });

                // subscription_packages
                Route::group(['prefix' => 'packages'], function () {
                    Route::get('/', [PackageController::class, 'index'])->name('saas.subscription_packages.index');
                    Route::get('edit/{id}', [PackageController::class, 'edit'])->name('saas.subscription_packages.edit');
                    Route::get('delete/{package}', [PackageController::class, 'delete'])->name('saas.subscription_packages.delete');
                    Route::get('create', [PackageController::class, 'create'])->name('saas.subscription_packages.create');
                    Route::post('store', [PackageController::class, 'store'])->name('saas.subscription_packages.store');
                    Route::post('update/{id}', [PackageController::class, 'update'])->name('saas.subscription_packages.update');
                    Route::post('delete-data', [PackageController::class, 'deleteData'])->name('saas.subscription_packages.delete_data');
                    Route::post('status-change', [PackageController::class, 'statusUpdate'])->name('saas.subscription_packages.statusUpdate');
                });

                // subscription_product_plans
                Route::group(['prefix' => 'product-plans'], function () {
                    Route::get('/', [ProductPlanController::class, 'index'])->name('saas.subscription_product_plans.index');
                    Route::get('{id}/edit', [ProductPlanController::class, 'edit'])->name('saas.subscription_product_plans.edit');
                    Route::get('{id}/delete', [ProductPlanController::class, 'delete'])->name('saas.subscription_product_plans.delete');
                    Route::get('create', [ProductPlanController::class, 'create'])->name('saas.subscription_product_plans.create');
                    Route::post('store', [ProductPlanController::class, 'store'])->name('saas.subscription_product_plans.store');
                    Route::get('create-modal', [ProductPlanController::class, 'createModal'])->name('saas.subscription_product_plans.create_modal');
                    Route::get('edit-modal/{id}', [ProductPlanController::class, 'editModal'])->name('saas.subscription_product_plans.edit_modal');
                    Route::post('{id}/update', [ProductPlanController::class, 'update'])->name('saas.subscription_product_plans.update');
                    Route::post('delete-data', [ProductPlanController::class, 'deleteData'])->name('saas.subscription_product_plans.delete_data');
                    Route::post('status-change', [ProductPlanController::class, 'statusUpdate'])->name('saas.subscription_product_plans.statusUpdate');
                });

                // subscription_package_plans
                Route::group(['prefix' => 'package-plans'], function () {
                    Route::get('/', [PackagePlanController::class, 'index'])->name('saas.subscription_package_plans.index');
                    Route::get('{id}/edit', [PackagePlanController::class, 'edit'])->name('saas.subscription_package_plans.edit');
                    Route::get('{id}/delete', [PackagePlanController::class, 'delete'])->name('saas.subscription_package_plans.delete');
                    Route::get('create', [PackagePlanController::class, 'create'])->name('saas.subscription_package_plans.create');
                    Route::post('store', [PackagePlanController::class, 'store'])->name('saas.subscription_package_plans.store');
                    Route::post('update', [PackagePlanController::class, 'update'])->name('saas.subscription_package_plans.update');
                });

                // subscription_purchased_packages
                Route::group(['prefix' => 'purchased-packages'], function () {
                    Route::get('/', [PurchasedPackageController::class, 'index'])->name('saas.subscription_purchased_packages.index');
                    Route::get('{id}/edit', [PurchasedPackageController::class, 'edit'])->name('saas.subscription_purchased_packages.edit');
                    Route::get('{id}/delete', [PurchasedPackageController::class, 'delete'])->name('saas.subscription_purchased_packages.delete');
                    Route::get('create', [PurchasedPackageController::class, 'create'])->name('saas.subscription_purchased_packages.create');
                    Route::post('store', [PurchasedPackageController::class, 'store'])->name('saas.subscription_purchased_packages.store');
                    Route::post('update', [PurchasedPackageController::class, 'update'])->name('saas.subscription_purchased_packages.update');
                });

                
                // cms
                Route::group(['prefix' => 'cms'], function () {
                    Route::get('/', [CmsController::class, 'index'])->name('saas.cms.index');
                    Route::get('{id}/edit', [CmsController::class, 'edit'])->name('saas.cms.edit');
                    Route::get('delete/{id}', [CmsController::class, 'delete'])->name('saas.cms.delete');
                    Route::get('create', [CmsController::class, 'create'])->name('saas.cms.create');
                    Route::post('store', [CmsController::class, 'store'])->name('saas.cms.store');
                    Route::post('update/{id}', [CmsController::class, 'update'])->name('saas.cms.update');
                });

                // pricing_plan
                Route::group(['prefix' => 'pricing-plans'], function () {
                    Route::get('/', [PricingPlanController::class, 'index'])->name('saas.pricing-plans.index');
                    Route::get('{id}/show', [PricingPlanController::class, 'show'])->name('saas.pricing-plans.show');
                    Route::get('{id}/edit', [PricingPlanController::class, 'edit'])->name('saas.pricing-plans.edit');
                    Route::get('delete/{id}', [PricingPlanController::class, 'delete'])->name('saas.pricing-plans.delete');
                    Route::get('create', [PricingPlanController::class, 'create'])->name('saas.pricing-plans.create');
                    Route::post('store', [PricingPlanController::class, 'store'])->name('saas.pricing-plans.store');
                    Route::post('update/{id}', [PricingPlanController::class, 'update'])->name('saas.pricing-plans.update');


                    Route::get('is-plan-name-exist', [PricingPlanController::class, 'isPlanNameExist'])->name('saas.is-plan-name-exist');
                });

                // Report
                Route::group(['prefix' => 'report'], function () {
                    Route::get('transactions', [ReportController::class, 'transactionReport'])->name('saas.report.transactions');
                });




                // Email Template
                Route::group(['prefix' => 'email-template'], function () {
                    Route::any('/',                     [EmailTemplateController::class, 'index'])->name('saas.email-template.index');
                    Route::get('create',                [EmailTemplateController::class, 'create'])->name('saas.email-template.create');
                    Route::get('{id}/show',             [EmailTemplateController::class, 'show'])->name('saas.email-template.show');
                    Route::get('{id}/edit',             [EmailTemplateController::class, 'edit'])->name('saas.email-template.edit');
                    Route::get('delete/{id}',           [EmailTemplateController::class, 'delete'])->name('saas.email-template.delete');
                    Route::post('store',                [EmailTemplateController::class, 'store'])->name('saas.email-template.store');
                    Route::post('update/{id}',          [EmailTemplateController::class, 'update'])->name('saas.email-template.update');
                });
                

                Route::group(['prefix' => 'contact-messages'], function () {
                    Route::get('/', [ContactMessageController::class, 'index'])->name('saas.contact-messages.index');
                    Route::get('delete/{id}', [ContactMessageController::class, 'delete'])->name('saas.contact-messages.delete');
                });

                Route::group(['prefix' => 'subscribers'], function () {
                    Route::get('/', [SubscriberController::class, 'index'])->name('saas.subscribers.index');
                    Route::get('delete/{id}', [SubscriberController::class, 'delete'])->name('saas.subscribers.delete');
                });

            });

        });


        Route::prefix('saas')->group(function () {

            // Route::group(['prefix' => 'single-company', 'as' => 'single-company.'], function () {
            //     Route::get('subscriptions', [SingleCompanySubscriptionController::class, 'index'])->name('subscriptions.index');
            // });

            Route::any('/', 'SubscriptionController@index');
            
            Route::prefix('currencies')->group(function () {
                Route::any('/', 'SubscriptionCurrencyController@index')->name('subscription.currency');
                Route::any('/', 'SubscriptionCurrencyController@index')->name('subscription.payment_method');
            });
            Route::prefix('products')->group(function () {
                Route::any('/', 'SubscriptionProductController@index')->name('subscription.products');
                Route::any('/plans', 'SubscriptionProductPlanController@index')->name('subscription.productPlans');

            });
            Route::prefix('modules')->group(function () {
                Route::any('/', 'SubscriptionModuleController@index')->name('subscription.module');
                Route::any('/details', 'SubscriptionModuleDetailController@index')->name('subscription.moduleDetail');
                Route::any('/categories', 'SubscriptionModuleCategoryController@index')->name('subscription.moduleCategory');
                Route::any('/terms', 'SubscriptionModuleTermController@index')->name('subscription.moduleTerm');
                Route::any('/values', 'SubscriptionModuleValueController@index')->name('subscription.moduleValue');

            });
            Route::prefix('packages')->group(function () {
                Route::any('/', 'SubscriptionPackageController@index')->name('subscription.packages');
                Route::any('/details', 'SubscriptionPackageDetailController@index')->name('subscription.packageDetails');
                Route::any('/plans', 'SubscriptionPackagePlanController@index')->name('subscription.packagePlans');

            });

            Route::prefix('payments')->group(function () {
                Route::any('/', 'SubscriptionPaymentController@index')->name('subscription.payments');
                Route::any('/details', 'SubscriptionPaymentDetailController@index')->name('subscription.paymentDetails');
                Route::any('/plans', 'SubscriptionPackagePlanController@index')->name('subscription.packagePlans');

            });

            Route::prefix('payouts')->group(function () {
                Route::any('/', 'SubscriptionPayoutController@index')->name('subscription.payouts');
                Route::any('/methods', 'SubscriptionPayoutMethodController@index')->name('subscription.payoutMethods');

            });

            Route::prefix('purchased-package')->group(function () {
                Route::any('/', 'SubscriptionPurchasedPackageController@index')->name('subscription.Purchasedpackages');
                Route::any('/details', 'SubscriptionPackageDetailController@index')->name('subscription.packageDetails');
                Route::any('/plans', 'SubscriptionPackagePlanController@index')->name('subscription.packagePlans');

            });
            Route::any('user-payout/methods', 'SubscriptionUserPayoutMethodController@index')->name('subscription.userPayoutMethods');

        });

        Route::Post('change-company', [SaasCompanyController::class, 'changeCompany'])->name('company.ajaxCompanyChange');  
    } else {
        Route::get('/', [LoginController::class, 'adminLogin'])->name('adminLogin')->middleware('xss');
    }
}else{
        Route::get('/', [HomeController::class, 'index'])->name('home');
}

Route::middleware(['demo.mode'])->group(function () {
    Route::prefix('tenantapi')->group(function () {
        Route::get('/', 'TenantApiController@index');
    });
});

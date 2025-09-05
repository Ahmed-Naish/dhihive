<?php

namespace Modules\Saas\Providers;

use App\Models\Content\AllContent;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\ServiceProvider;
use Modules\Saas\Repositories\CmsRepository;
use Modules\Saas\Repositories\CmsRepositoryInterface;
use Modules\Saas\Repositories\PricingPlansRepository;
use Modules\Saas\Repositories\EmailTemplateRepository;
use Modules\Saas\Events\SendSubscriptionSuccessMailEvent;
use Modules\Saas\Events\SendSubscriptionApprovedMailEvent;
use Modules\Saas\Events\SendSubscriptionRejectedMailEvent;
use Modules\Saas\Events\SendSubscriptionUpgradedMailEvent;
use Modules\Saas\Events\SubscriptionApproveEvent;
use Modules\Saas\Repositories\PricingPlansRepositoryInterface;
use Modules\Saas\Listeners\SendSubscriptionSuccessMailListener;
use Modules\Saas\Repositories\EmailTemplateRepositoryInterface;
use Modules\Saas\Http\Repositories\SubscriptionModuleRepository;
use Modules\Saas\Listeners\SendSubscriptionApprovedMailListener;
use Modules\Saas\Listeners\SendSubscriptionRejectedMailListener;
use Modules\Saas\Listeners\SendSubscriptionUpgradedMailListener;
use Modules\Saas\Http\Repositories\SubscriptionPackageRepository;
use Modules\Saas\Http\Repositories\SubscriptionProductRepository;
use Modules\Saas\Repositories\SubscriptionModuleDetailsRepository;
use Modules\Saas\Repositories\SubscriptionPaymentMethodRepository;
use Modules\Saas\Http\Repositories\SubscriptionModuleTermRepository;
use Modules\Saas\Repositories\SubscriptionModuleRepositoryInterface;
use Modules\Saas\Http\Repositories\SubscriptionModuleValueRepository;
use Modules\Saas\Http\Repositories\SubscriptionPackagePlanRepository;
use Modules\Saas\Http\Repositories\SubscriptionProductPlanRepository;
use Modules\Saas\Repositories\SubscriptionPackageRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionProductRepositoryInterface;
use Modules\Saas\Http\Repositories\SubscriptionModuleCategoryRepository;
use Modules\Saas\Repositories\SubscriptionModuleTermRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionModuleValueRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionPackagePlanRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionProductPlanRepositoryInterface;
use Modules\Saas\Http\Repositories\SubscriptionPurchasedPackageRepository;
use Modules\Saas\Listeners\SubscriptionApproveListener;
use Modules\Saas\Repositories\SubscriptionModuleDetailsRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionPaymentMethodRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionModuleCategoryRepositoryInterface;
use Modules\Saas\Repositories\SubscriptionPurchasedPackageRepositoryInterface;

class SaasServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Saas';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'saas';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $this->app['events']->listen(SendSubscriptionSuccessMailEvent::class, SendSubscriptionSuccessMailListener::class);
        $this->app['events']->listen(SendSubscriptionApprovedMailEvent::class, SendSubscriptionApprovedMailListener::class);
        $this->app['events']->listen(SendSubscriptionUpgradedMailEvent::class, SendSubscriptionUpgradedMailListener::class);
        $this->app['events']->listen(SendSubscriptionRejectedMailEvent::class, SendSubscriptionRejectedMailListener::class);
        $this->app['events']->listen(SubscriptionApproveEvent::class, SubscriptionApproveListener::class);

        $data['contents'] = [];

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/Saas'),
        ], 'Saas');
        $this->publishes([
            __DIR__ . '/../Assets' => public_path('vendor/Saas/Assets'),
        ], 'Saas');

        try {
            if (Schema::hasTable('all_contents')) {
                $data['contents'] = AllContent::where('status_id', 1)->select('title', 'slug')->get();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        View::share('contents', $data['contents']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(
            SubscriptionPaymentMethodRepositoryInterface::class,
            SubscriptionPaymentMethodRepository::class
        );
        $this->app->bind(
            SubscriptionProductRepositoryInterface::class,
            SubscriptionProductRepository::class
        );
        $this->app->bind(
            SubscriptionModuleRepositoryInterface::class,
            SubscriptionModuleRepository::class
        );
        $this->app->bind(
            SubscriptionModuleCategoryRepositoryInterface::class,
            SubscriptionModuleCategoryRepository::class
        );
        $this->app->bind(
            SubscriptionModuleTermRepositoryInterface::class,
            SubscriptionModuleTermRepository::class
        );
        $this->app->bind(
            SubscriptionModuleValueRepositoryInterface::class,
            SubscriptionModuleValueRepository::class
        );
        $this->app->bind(
            SubscriptionPackageRepositoryInterface::class,
            SubscriptionPackageRepository::class
        );
        $this->app->bind(
            SubscriptionPackagePlanRepositoryInterface::class,
            SubscriptionPackagePlanRepository::class
        );
        $this->app->bind(
            SubscriptionModuleDetailsRepositoryInterface::class,
            SubscriptionModuleDetailsRepository::class
        );
        $this->app->bind(
            SubscriptionPurchasedPackageRepositoryInterface::class,
            SubscriptionPurchasedPackageRepository::class
        );
        $this->app->bind(
            SubscriptionProductPlanRepositoryInterface::class,
            SubscriptionProductPlanRepository::class
        );
        $this->app->bind(
            CmsRepositoryInterface::class,
            CmsRepository::class
        );
        $this->app->bind(
            PricingPlansRepositoryInterface::class,
            PricingPlansRepository::class
        );

        $this->app->bind(
            EmailTemplateRepositoryInterface::class,
            EmailTemplateRepository::class
        );
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}

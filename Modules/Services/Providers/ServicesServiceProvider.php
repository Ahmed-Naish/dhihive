<?php

namespace Modules\Services\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Services\Repositories\BrandRepository;
use Modules\Services\Repositories\ModelRepository;
use Modules\Services\Repositories\MachineRepository;
use Modules\Services\Repositories\PackageRepository;
use Modules\Services\Repositories\ServiceRepository;
use Modules\Services\Repositories\InstitutionRepository;
use Modules\Services\Repositories\BrandRepositoryInterface;
use Modules\Services\Repositories\ModelRepositoryInterface;
use Modules\Services\Repositories\PackageDetailsRepository;
use Modules\Services\Repositories\MachineRepositoryInterface;
use Modules\Services\Repositories\PackageRepositoryInterface;
use Modules\Services\Repositories\ServiceRepositoryInterface;
use Modules\Services\Repositories\InstitutionRepositoryInterface;
use Modules\Services\Repositories\PackageDetailsRepositoryInterface;
use Modules\Services\Repositories\ServiceDetailsRepository;
use Modules\Services\Repositories\ServiceDetailsRepositoryInterface;

class ServicesServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Services';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'services';

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
            ModelRepositoryInterface::class,
            ModelRepository::class

        );
        $this->app->bind(
            BrandRepositoryInterface::class,
            BrandRepository::class

        );
        $this->app->bind(
            InstitutionRepositoryInterface::class,
            InstitutionRepository::class

        );
        $this->app->bind(
            MachineRepositoryInterface::class,
            MachineRepository::class

        );
        $this->app->bind(
            PackageRepositoryInterface::class,
            PackageRepository::class

        );
        $this->app->bind(
            ServiceRepositoryInterface::class,
            ServiceRepository::class

        );
        $this->app->bind(
            PackageDetailsRepositoryInterface::class,
            PackageDetailsRepository::class

        );
        $this->app->bind(
            ServiceDetailsRepositoryInterface::class,
            ServiceDetailsRepository::class

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
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
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
            $sourcePath => $viewPath
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
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
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

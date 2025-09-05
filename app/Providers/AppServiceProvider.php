<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Settings\HrmLanguage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use App\Models\coreApp\Setting\Setting;
use Illuminate\Support\ServiceProvider;
use App\Helpers\CoreApp\Traits\DateHandler;
use App\Helpers\CoreApp\Traits\GeoLocationTrait;
use App\Helpers\CoreApp\Traits\TimeDurationTrait;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Models\coreApp\Relationship\RelationshipTrait;
use App\Repositories\DailyLeave\EloquentDailyLeaveRepository;
use App\Repositories\DailyLeave\DailyLeaveRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    use ApiReturnFormatTrait, RelationshipTrait, TimeDurationTrait, GeoLocationTrait, DateHandler;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Repositories\Interfaces\TeamInterface::class,
            \App\Repositories\Team\TeamRepository::class
        );
        $this->app->bind(DailyLeaveRepositoryInterface::class, EloquentDailyLeaveRepository::class);

    }
    public function boot()
    {
        try {

            DB::connection()->getPdo();
            if (Schema::hasTable('settings')) {
                $settings = Setting::where('company_id', 1)->pluck('value', 'name');
                foreach ($settings as $key => $value) {
                    config()->set("settings.app.{$key}", $value);
                }

                config([
                    'mail.default' => base_settings('mail_mailer'),
                    'mail.mailers.smtp.host' => base_settings('mail_host'),
                    'mail.mailers.smtp.port' => base_settings('mail_port'),
                    'mail.mailers.smtp.encryption' => base_settings('mail_encryption'),
                    'mail.mailers.smtp.username' => base_settings('mail_username'),
                    'mail.mailers.smtp.password' => base_settings('mail_password'),
                    'mail.from.address' => base_settings('mail_from_address'),
                    'mail.from.name' => base_settings('mail_from_name'),

                    'filesystems.default' => base_settings('file_system_driver'),

                    'services.firebase.api_key' => base_settings('firebase_api_key'),
                    'services.firebase.auth_domain' => base_settings('firebase_auth_domain'),
                    'services.firebase.database_url' => base_settings('firebase_auth_database_url'),
                    'services.firebase.project_id' => base_settings('firebase_project_id'),
                    'services.firebase.storage_bucket' => base_settings('firebase_storage_bucket'),
                    'services.firebase.messaging_sender_id' => base_settings('firebase_messaging_sender_id'),
                    'services.firebase.app_id' => base_settings('firebase_app_id'),
                    'services.firebase.measurement_id' => base_settings('firebase_measurement_id'),

                    'broadcasting.connections.pusher.app_id' => base_settings('pusher_app_id'),
                    'broadcasting.connections.pusher.key' => base_settings('pusher_app_key'),
                    'broadcasting.connections.pusher.secret' => base_settings('pusher_app_secret'),
                    'broadcasting.connections.pusher.options.cluster' => base_settings('pusher_app_cluster'),
                ]);

                if (base_settings('file_system_driver') == 's3') {
                    config([
                        'filesystems.disks.s3.key' => base_settings('aws_access_key_id'),
                        'filesystems.disks.s3.secret' => base_settings('aws_secret_access_key'),
                        'filesystems.disks.s3.region' => base_settings('aws_default_region'),
                        'filesystems.disks.s3.bucket' => base_settings('aws_bucket'),
                        'filesystems.disks.s3.url' => base_settings('aws_url'),
                        'filesystems.disks.s3.endpoint' => base_settings('aws_endpoint'),
                        'filesystems.disks.s3.use_path_style_endpoint' => base_settings('aws_use_path_style_endpoint') ? true : false,
                    ]);
                }
            }

            if (Schema::hasTable('company_configs')) {
                config(['app.timezone' => settings('timezone')]);
            }

            //app singleton
            $this->app->singleton('settings', function () {
                return Setting::get()->pluck('value', 'name');
            });
            $this->app->singleton('hrm_languages', function () {
                return HrmLanguage::with('language')->where('status_id', 1)->get();
            });
            
            if (env('APP_HTTPS') == true) {
                URL::forceScheme('https');
                $this->app['request']->server->set('HTTPS', true);
            }

            Paginator::useBootstrapFive();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }
}

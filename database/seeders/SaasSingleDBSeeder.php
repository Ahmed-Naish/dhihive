<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\UserSeeder;
use Database\Seeders\Admin\StatusSeeder;
use Database\Seeders\Admin\CompanySeeder;
use Database\Seeders\Admin\PermissionSeeder;
use Database\Seeders\Hrm\Country\CountrySeeder;
use Modules\Saas\Database\Seeders\CmsTableSeeder;
use Database\Seeders\Hrm\AppSetting\AppScreenSeeder;
use Modules\Saas\Database\Seeders\EmailTemplateSeeder;

class SaasSingleDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CountrySeeder::class,
            StatusSeeder::class,
            CompanySeeder::class,
            BranchSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            SettingsSeeder::class,
            UploadSeeder::class,
            CompanyConfigSeeder::class,
            AllContentsTableSeeder::class,
            CmsTableSeeder::class,
            EmailTemplateSeeder::class
        ]);
    }
}

<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Database\Seeders\Hrm\AllContentSeeder;
use Modules\Saas\Database\Seeders\CmsTableSeeder;
use Database\Seeders\Hrm\AppSetting\AppScreenSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleSeeder;
use Modules\Saas\Database\Seeders\SubscriptionProductSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleTermsSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleValueSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleDetailsSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleCategorySeeder;
use Modules\Saas\Database\Seeders\UserPayoutMethodSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPayoutSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPackageSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionCurrencySeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleTermsSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleValueSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPackagePlanSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionProductPlanSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPayoutMethodSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionModuleDetailsSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPaymentMethodSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPackageDetailsSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPaymentDetailsSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPurchasedPackageSeederTableSeeder;
use Modules\Saas\Database\Seeders\SubscriptionPurchasedPackageDetailsSeederTableSeeder;

class SaasDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
    }
}

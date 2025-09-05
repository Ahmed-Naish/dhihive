<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServicePackageDetailSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        $servicePackageDetailsData = [
            [
                'package_id' => 1,
                'machine_id' => 1,
                'model_id' => 1,
                'brand_id' => 1,
                'quantity' => 10,
                'warranty_period' => 12,
                'status_id' => '1',
            ],
            [
                'package_id' => 2,
                'machine_id' => 2,
                'model_id' => 2,
                'brand_id' => 2,
                'quantity' => 5,
                'warranty_period' => 24,
                'status_id' => '1',
            ],
            // Add more service package data as needed
        ];

        DB::table('service_package_details')->insert($servicePackageDetailsData);
    }
}

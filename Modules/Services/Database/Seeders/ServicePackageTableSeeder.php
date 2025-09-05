<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServicePackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");

        $servicePackagesData = [
            [
                'name' => 'Package 1',
                'package_details_id' => 1,
                'origin' => 'Country A',
                'package_no' => 'sp-1202',
                'contract_date' => '2023-07-19',
                'status_id' => '1',
            ],
            [
                'name' => 'Package 2',
                'package_details_id' => 2,
                'origin' => 'Country B',
                'package_no' => 'sp-1502',
                'contract_date' => '2023-08-01',
                'status_id' => '1',
            ],
            // Add more service package data as needed
        ];

        DB::table('service_packages')->insert($servicePackagesData);
    }
}

<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServiceTableSeeder extends Seeder
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

        $servicesData = [
            [
                'institution_id' => 1,
                'package_id' => 1,
                'installation_date' => '2023-07-19',
                'contract_person_id' => 1,
                'status_id' => '1',
            ],
            [
                'institution_id' => 2,
                'package_id' => 2,
                'installation_date' => '2023-08-01',
                'contract_person_id' => 2,
                'status_id' => '1',
            ],
            // Add more service data as needed
        ];

        DB::table('module_services')->insert($servicesData);
    }
}

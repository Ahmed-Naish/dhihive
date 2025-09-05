<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServiceModelTableSeeder extends Seeder
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

        $serviceModelsData = [
            [
                'name' => 'Model 1',
                'status_id' => '1',
            ],
            [
                'name' => 'Model 2',
                'status_id' => '1',
            ],
            // Add more service Model data as needed
        ];

        DB::table('service_models')->insert($serviceModelsData);
    }
}

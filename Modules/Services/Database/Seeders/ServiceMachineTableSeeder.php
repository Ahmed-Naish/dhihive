<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServiceMachineTableSeeder extends Seeder
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

        $machinesData = [
            [
                'machine_name' => 'Machine 1',
                'brand_id' => 1,
                'model_id' => 1,
                'origin' => 'Country A',
                'status_id' => '1',
            ],
            [
                'machine_name' => 'Machine 2',
                'brand_id' => 2,
                'model_id' => 2,
                'origin' => 'Country B',
                'status_id' => '1',
            ],
            // Add more machine data as needed
        ];

        DB::table('service_machines')->insert($machinesData);
    }
}

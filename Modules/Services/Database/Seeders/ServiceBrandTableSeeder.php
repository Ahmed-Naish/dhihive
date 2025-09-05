<?php

namespace Modules\Services\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ServiceBrandTableSeeder extends Seeder
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

        $serviceBrandsData = [
            [
                'name' => 'Brand 1',
                'status_id' => '1',
            ],
            [
                'name' => 'Brand 2',
                'status_id' => '1',
            ],
            // Add more service brand data as needed
        ];

        DB::table('service_brands')->insert($serviceBrandsData);
    }
}

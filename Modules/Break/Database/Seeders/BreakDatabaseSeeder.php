<?php

namespace Modules\Break\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model; 
use Modules\Break\Database\Seeders\BreakTypesTableSeeder;
use Modules\Break\Database\Seeders\UserBreaksTableSeeder;

class BreakDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(BreakSettingSeeder::class);
        $this->call(BreakTypesTableSeeder::class);
        // $this->call(UserBreaksTableSeeder::class);
    }
}

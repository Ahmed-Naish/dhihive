<?php

namespace Database\Seeders;

use App\Models\Hrm\Designation\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input      = session()->get('input');
        $company_id = $input['company_id'] ?? 1;
        $branch_id  = $input['branch_id'] ?? 1;

        $designations = [
            'CEO',
            'Managing Director',
            'CIO',
            'CTO',
            'HR Manager',
            'HR Executive',
            'Finance Manager',
            'Accountant',
            'IT Manager',
            'Software Developer',
            'Operations Manager',
            'Logistics Coordinator',
            'Sales Manager',
            'Sales Representative'
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate([
                'title'         => $designation,
                'company_id'    => $company_id,
                'branch_id'     => $branch_id,
            ], [
                'status_id'     => 1,
            ]);
        }        
    }
}

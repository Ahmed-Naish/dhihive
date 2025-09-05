<?php

namespace Database\Seeders;

use App\Models\Hrm\Department\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
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

        $departments = [
            'Executive',
            'HR',
            'Finance',
            'IT',
            'Operations',
            'Sales'
        ]; 

        foreach ($departments as $department) {
            Department::firstOrCreate([
                'title'         => $department,
                'company_id'    => $company_id,
                'branch_id'     => $branch_id,
            ], [
                'status_id'     => 1,
            ]);
        }
    }
}

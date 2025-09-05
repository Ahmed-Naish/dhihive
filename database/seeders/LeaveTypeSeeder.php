<?php

namespace Database\Seeders;

use App\Models\Hrm\Leave\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = session()->get('input');

        $leaveTypes = [
            'Casual Leave',
            'Sick Leave',
            'Maternity Leave',
            'Paternity Leave',
            'Leave Without Pay',
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate([
                'name'          => $leaveType,
                'company_id'    => @$input['company_id'] ?? 1,
                'branch_id'     => @$input['branch_id'] ?? 1,
            ], [
                'status_id'     => 1
            ]);
        }
    }
}

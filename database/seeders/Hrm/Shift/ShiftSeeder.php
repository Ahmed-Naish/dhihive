<?php

namespace Database\Seeders\Hrm\Shift;

use App\Models\Hrm\Shift\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
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

        $shifts = [
            'Morning',
            'Evening',
            'Night',
        ];

        foreach($shifts as $shift) {
            Shift::firstOrCreate([
                'name'          => $shift,
                'company_id'    => $company_id,
                'branch_id'     => $branch_id,
            ], [
                'status_id'     => 1,
            ]);
        }

        if ($input) {
            $input['shift_id'] = 1;
            session()->put('input', $input);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
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
        $user_id    = $input['user_id'] ?? 1;

        $branch             = Branch::firstOrCreate([
            'name'          => 'Head Office',
            'company_id'    => $company_id,
        ],[
            'address'       => $input['address'] ?? "Unknown Street, Texas, USA",
            'phone'         => $input['phone'] ?? '0XXXXXXXXXX',
            'email'         => $input['email'] ?? "hello@gmail.test",
            'user_id'       => $user_id,
        ]);

        if ($input) {
            $input['branch_id'] = $branch->id;
            session()->put('input', $input);
        }
    }
}

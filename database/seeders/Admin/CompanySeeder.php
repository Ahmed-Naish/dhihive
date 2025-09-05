<?php

namespace Database\Seeders\Admin;

use App\Models\Branch;
use App\Models\Company\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $input = session()->get('input');

        if ($input) {
            try {
                $company = Company::create([
                    'saas_company_id'      => @$input['saas_company_id'],
                    'country_id'           => $input['country_id'] ?? 1,
                    'name'                 => $input['name'] ?? 'Mr Owner',
                    'company_name'         => $input['company_name'] ?? 'Company',
                    'email'                => $input['email'] ?? 'hello@gmail.test',
                    'phone'                => $input['phone'] ?? '0XXXXXXXXXX',
                    'total_employee'       => $input['total_employee'] ?? 10,
                    'trade_licence_number' => $input['trade_licence_number'] ?? '1234567890',
                    'business_type'        => $input['business_type'] ?? 'Unknown',
                    'subdomain'            => $input['subdomain'] ?? 'test-com',
                    'is_main_company'      => 'no',
                    'is_subscription'      => @$input['is_subscription'] ?? 0,
                ]);

                $input['company_id'] = $company->id;
                session()->put('input', $input);
            } catch (\Throwable $th) {}
        } else {
            try {
                $company = Company::firstOrCreate([
                    'name'              => 'Mr Owner',
                    'company_name'      => 'Main Company',
                    'email'             => 'hello@onesttech.com',
                ], [
                    'phone'             => '0XXXXXXXXXX',
                    'total_employee'    => 100,
                    'business_type'     => 'Service',
                    'is_main_company'   => 'yes',
                    'country_id'        => 223,
                ]);
            } catch (\Throwable $th) {
                Log::error('Company Seed Error');
            }
        }
    }
}

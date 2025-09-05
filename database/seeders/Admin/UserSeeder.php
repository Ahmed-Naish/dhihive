<?php

namespace Database\Seeders\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Helpers\CoreApp\Traits\PermissionTrait;
use App\Models\UserShiftAssign;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{

    use PermissionTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            foreach($this->userData() ?? [] as $key => $user) {
                $newUser                 = User::firstOrCreate([
                    'email'              => $user['email'],
                ], [
                    'name'               => $user['name'],
                    'is_admin'           => @$user['is_admin'],
                    'is_hr'              => @$user['is_hr'],
                    'role_id'            => @$user['role_id'],
                    'company_id'         => @$user['company_id'],
                    'country_id'         => @$user['country_id'],
                    'department_id'      => @$user['department_id'],
                    'designation_id'     => @$user['designation_id'],
                    'phone'              => @$user['phone'],
                    'permissions'        => @$user['permissions'],
                    'email_verified_at'  => now(),
                    'remember_token'     => Str::random(10),
                    'is_email_verified'  => 'verified',
                    'email_verify_token' => Str::random(10),
                    'password'           => Hash::make('12345678'),
                    'attendance_method'  => ['normal_attendance' => 1],
                ]);

                if (Schema::hasTable('user_shift_assigns') && @$user['shift_id']) {
                    UserShiftAssign::create([
                        'user_id'   => $newUser->id,
                        'shift_id'  => @$user['shift_id'],
                    ]);
                }
            }

            $input = session()->get('input');
            if ($input) {
                $input['user_id'] = 1;
                session()->put('input', $input);
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    protected function userData()
    {
        $input = session()->get('input');
        
        return [
            [
                'name'           => $input['name'] ?? 'Mr Super Admin',
                'email'          => $input['email'] ?? 'admin@onesttech.com',
                'phone'          => $input['phone'] ?? '0XXXXXXXXXXX',
                'is_admin'       => 1,
                'is_hr'          => 0,
                'role_id'        => 1,
                'company_id'     => @$input['company_id'] ?? 1,
                'branch_id'      => @$input['branch_id'] ?? 1,
                'country_id'     => 223,
                'shift_id'       => 1,
                'department_id'  => 1,
                'designation_id' => 1,
                'permissions'    => json_encode($this->adminPermissions()),
            ]
        ];
    }
}

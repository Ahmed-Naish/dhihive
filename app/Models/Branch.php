<?php

namespace App\Models;

use App\Models\Role\Role;
use App\Models\coreApp\BaseModel;
use App\Models\Hrm\Attendance\Weekend;
use App\Models\Hrm\Department\Department;
use App\Models\Hrm\Designation\Designation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Branch extends BaseModel
{
    use StatusRelationTrait, SoftDeletes, HasFactory;

    protected $fillable = [
        'id', 'name', 'address', 'email', 'phone', 'user_id', 'company_id', 'status_id'
    ];

    //boot function
    protected static function boot()
    {
        parent::boot();

        //get this class instance
        if (auth()->check() && !config('app.single_db')) {
            static::creating(function ($model) {
                $model->company_id = getCurrentCompany();
                $model->user_id = auth()->user()->id ?? 1;

                $weekdays = [
                    'saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday',
                ];
                $crated_company_id = auth()->check() ? auth()->user()->company_id : 1;

                foreach ($weekdays as $day) {
                    $isWeekend = 'no';
                    if ($day == 'friday') {
                        $isWeekend = 'yes';
                    }
                    Weekend::create([
                        'name' => $day,
                        'is_weekend' => $isWeekend,
                        'status_id' => 1,
                        'company_id' => $crated_company_id,
                        'branch_id' => $model->id,
                    ]);
                }

                //Company Config data clone
                // $default_company_configs = CompanyConfig::where('company_id', $crated_company_id)->get();
                // foreach ($default_company_configs as $key => $config) {

                //     $company_config = new CompanyConfig;
                //     $company_config->key = $config->key;
                //     $company_config->value = $config->value;
                //     $company_config->save();

                //     $company_config->company_id = $crated_company_id;
                //     $company_config->update();
                // }
            });
        }
    }

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'branch_id');
    }

    public function designations()
    {
        return $this->hasMany(Designation::class, 'branch_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'branch_id');
    }
}

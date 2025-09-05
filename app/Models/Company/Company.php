<?php

namespace App\Models\Company;

use App\Models\User;
use App\Models\Branch;
use App\Models\Role\Role;
use App\Models\coreApp\BaseModel;
use Spatie\Activitylog\LogOptions;
use App\Models\Hrm\Country\Country;
use App\Models\Hrm\Attendance\Weekend;
use App\Models\coreApp\Setting\Setting;
use App\Models\Hrm\Department\Department;
use App\Models\Hrm\Designation\Designation;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\coreApp\Setting\CompanyConfig;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Company extends BaseModel
{
    use HasFactory, StatusRelationTrait, LogsActivity, SoftDeletes;

    protected $fillable = [
        'id', 'name', 'company_name', 'email', 'phone', 'total_employee', 'business_type', 'trade_licence_number', 'subdomain', 'status_id', 'trade_licence_id', 'is_main_company', 'country_id', 'is_subscription',
    ];

    protected static $logAttributes = [
        'id', 'name', 'company_name', 'email', 'phone', 'total_employee', 'business_type', 'trade_licence_number', 'subdomain', 'status_id', 'trade_licence_id', 'is_main_company', 'country_id', 'is_subscription'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'company_id', 'id');
    }

    public function weekends(): HasMany
    {
        return $this->hasMany(Weekend::class, 'company_id', 'id');
    }

    public function configs(): HasMany
    {
        return $this->hasMany(CompanyConfig::class, 'company_id', 'id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'company_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'company_id', "id");
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class, 'company_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = LogOptions::defaults();

        return $logOptions;
    }
}

<?php

namespace App\Models\Role;

use App\Models\coreApp\BaseModel;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Role extends BaseModel
{
    use HasFactory, StatusRelationTrait, LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'slug', 'permissions', 'status_id', 'company_id', 'upper_roles','app_login',
    'web_login', 'branch_id'];

    protected static $logAttributes = [
        'company_id', 'name', 'slug', 'permissions', 'status_id',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    public function scopeBranch($query)
    {
        return $query->where('branch_id', userBranch());
    }

    //boot function
    protected static function boot()
    {
        parent::boot();

        static::created(function ($role) {
            Cache::forget('all_roles');
        });
        static::updated(function ($role) {
            Cache::forget('all_roles');
        });
        static::deleted(function ($role) {
            Cache::forget('all_roles');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

}

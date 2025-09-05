<?php

namespace App\Models\Hrm\Attendance;

use App\Models\coreApp\BaseModel;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Weekend extends BaseModel
{
    use HasFactory, StatusRelationTrait, LogsActivity;

    protected $fillable = [
        'company_id', 'name', 'is_weekend', 'status_id', 'branch_id',
    ];

    protected static $logAttributes = [
        'company_id', 'name', 'is_weekend', 'status_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

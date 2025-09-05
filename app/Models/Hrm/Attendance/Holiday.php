<?php

namespace App\Models\Hrm\Attendance;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use Spatie\Activitylog\LogOptions;
use App\Models\Traits\CompanyTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Holiday extends BaseModel
{
    use HasFactory, StatusRelationTrait,LogsActivity,CompanyTrait,BranchTrait;

    protected $fillable = [
        'company_id', 'title', 'description', 'start_date', 'end_date', 'attachment_id', 'status_id'
    ];
    protected static $logAttributes = [
        'company_id', 'title', 'description', 'start_date', 'end_date', 'attachment_id', 'status_id'
    ];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}

<?php

namespace App\Models\Hrm\Shift;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use Spatie\Activitylog\LogOptions;
use App\Models\Traits\CompanyTrait;
use App\Models\Hrm\Attendance\DutySchedule;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Shift extends BaseModel
{
    use HasFactory, StatusRelationTrait, LogsActivity,CompanyTrait,BranchTrait;
    protected $fillable = ['id', 'company_id', 'name', 'status_id'];

    public function dutySchedule(): HasOne
    {
        return $this->hasOne(DutySchedule::class, 'shift_id', 'id');
    }

    protected static $logAttributes = [
       'company_id', 'id','title', 'status_id'
    ];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

}

<?php

namespace App\Models\Hrm\Notice;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\Hrm\Department\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeDepartment extends BaseModel
{
    use HasFactory, CompanyTrait, BranchTrait;
    protected $fillable=['noticeable_id','noticeable_type','department_id'];

    public function noticeable()
    {
        return $this->morphTo();
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

  

}

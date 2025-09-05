<?php

namespace App\Models\Hrm\Leave;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;

class LeaveSetting extends BaseModel
{
    use CompanyTrait,BranchTrait;

    protected $fillable = ['company_id', 'sandwich_leave', 'month', 'prorate_leave'];
}

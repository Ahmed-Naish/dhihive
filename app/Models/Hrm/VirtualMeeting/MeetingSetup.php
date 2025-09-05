<?php

namespace App\Models\Hrm\VirtualMeeting;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingSetup extends BaseModel
{
    use HasFactory,CompanyTrait,BranchTrait;
}

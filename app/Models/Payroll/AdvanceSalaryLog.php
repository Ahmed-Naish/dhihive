<?php

namespace App\Models\Payroll;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdvanceSalaryLog extends BaseModel
{
    use HasFactory,BranchTrait;
}

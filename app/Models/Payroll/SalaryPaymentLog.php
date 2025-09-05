<?php

namespace App\Models\Payroll;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryPaymentLog extends BaseModel
{
    use HasFactory,BranchTrait;
}

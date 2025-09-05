<?php

namespace App\Models;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalarySheetReport extends BaseModel
{
    use HasFactory,CompanyTrait;
}

<?php

namespace App\Models\Settings;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiSetup extends BaseModel
{
    use HasFactory,CompanyTrait;
}

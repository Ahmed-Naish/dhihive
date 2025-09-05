<?php

namespace App\Models\coreApp\Setting;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\CompanyTrait;

class CompanyConfig extends BaseModel
{
    use CompanyTrait;
    protected $fillable = ['key', 'value','company_id'];
}

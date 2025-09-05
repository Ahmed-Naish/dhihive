<?php

namespace App\Models\Hrm\AppSetting;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use App\Models\Traits\CompanyTrait;

class AppScreen extends BaseModel
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [
        'company_id', 'name', 'slug', 'position', 'icon', 'status_id'
    ];
}

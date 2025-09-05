<?php

namespace App\Models\coreApp\Setting;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class IpSetup extends BaseModel
{
    use HasFactory,CompanyTrait,StatusRelationTrait,BranchTrait;

    protected $fillable = [
        'location',
        'ip_address',
        'status_id',
        'company_id',
    ];
}

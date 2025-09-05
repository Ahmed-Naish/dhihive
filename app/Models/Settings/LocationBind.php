<?php

namespace App\Models\Settings;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationBind extends BaseModel
{
    use HasFactory,BranchTrait,CompanyTrait;

    
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}

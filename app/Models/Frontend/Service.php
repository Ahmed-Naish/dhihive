<?php

namespace App\Models\Frontend;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Service extends BaseModel
{
    use HasFactory,StatusRelationTrait;
}

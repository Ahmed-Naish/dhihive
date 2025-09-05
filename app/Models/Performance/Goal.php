<?php

namespace App\Models\Performance;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\Performance\GoalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goal extends BaseModel
{
    use HasFactory,BranchTrait,CompanyTrait;

    public function goalType()
    {
        return $this->belongsTo(GoalType::class);
    }
}

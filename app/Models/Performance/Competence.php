<?php

namespace App\Models\Performance;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\coreApp\Status\Status;
use App\Models\Performance\CompetenceType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Competence extends BaseModel
{
    use HasFactory,BranchTrait,CompanyTrait;

    public function competenceType()
    {
        return $this->belongsTo(CompetenceType::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}

<?php

namespace App\Models\Hrm\Appoinment;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppoinmentParticipant extends BaseModel
{
    use HasFactory, CompanyTrait, BranchTrait;

    public function participantInfo():BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
}

<?php

namespace App\Models\Performance;

use App\Models\User;
use App\Models\Hrm\Shift\Shift;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\Hrm\Department\Department;
use App\Models\Hrm\Designation\Designation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Indicator extends BaseModel
{
    use HasFactory,BranchTrait,CompanyTrait;

    protected $casts = [
        'rates'   => 'json',
    ];

    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class)->withDefault();
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class)->withDefault();
    }
    // shift
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function added(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    
}

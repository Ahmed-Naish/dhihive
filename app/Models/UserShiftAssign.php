<?php

namespace App\Models;

use App\Models\Hrm\Shift\Shift;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserShiftAssign extends BaseModel
{
    use HasFactory;


    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }
}

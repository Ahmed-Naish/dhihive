<?php

namespace App\Models\Visit;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitSchedule extends BaseModel
{
    use HasFactory;
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}

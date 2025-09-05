<?php

namespace App\Models\Hrm\Attendance;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeBreak extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'date',
        'break_time',
        'back_time',
        'reason'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

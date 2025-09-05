<?php

namespace App\Models\Hrm\Meeting;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingParticipant extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'meeting_id',
        'is_going',
        'is_present',
        'present_at',
        'meeting_started_at',
        'meeting_ended_at',
        'meeting_duration',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id', 'id')->select('id','name','email');
    }

    public function user():belongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }
}

<?php

namespace App\Models\Hrm\Meeting;

use App\Models\Upload;
use App\Models\User;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Meeting extends BaseModel
{
    use HasFactory, CompanyTrait, StatusRelationTrait,BranchTrait;

    protected $fillable = [
        'company_id',
        'user_id',
        'user_id',
        'description',
        'location',
        'date',
        'duration',
        'start_at',
        'end_at',
        'attachment_file_id',
        'status_id'
    ];

    public function meetingParticipants(): HasMany
    {
        return $this->hasMany(MeetingParticipant::class, 'meeting_id', 'id')->with('user');
    }

    public function meetingHost(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function attachment()
    {
        return $this->belongsTo(Upload::class, 'attachment_file_id', 'id');
    }


}

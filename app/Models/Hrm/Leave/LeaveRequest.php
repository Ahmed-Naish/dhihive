<?php

namespace App\Models\Hrm\Leave;

use App\Models\Upload;
use App\Models\User;
use App\Models\coreApp\BaseModel;
use App\Models\ActivityLogs\AuthorInfo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\UserRelationTrait;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;

class LeaveRequest extends BaseModel
{

    use HasFactory, StatusRelationTrait,UserRelationTrait, CompanyTrait, BranchTrait;

    protected $fillable = [
        'assign_leave_id',
        'user_id',
        'apply_date',
        'leave_from',
        'leave_to',
        'reason',
        'substitute_id',
        'attachment_file_id',
        'status_id',
        'author_info_id',
    ];


    public function assignLeave(): BelongsTo
    {
        return $this->belongsTo(AssignLeave::class);
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(User::class, 'substitute_id', 'id');
    }

    public function referredBy():HasOne
    {
        return $this->hasOne(AuthorInfo::class, 'referred_by','id');
    }
    public function approvedBy():HasOne
    {
        return $this->hasOne(AuthorInfo::class, 'approved_by', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->belongsTo(Upload::class,'attachment_file_id', 'id');
    }

}

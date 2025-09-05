<?php

namespace App\Models\Hrm\Support;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\coreApp\Traits\Relationship\UserRelationTrait;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;

class TicketReply extends BaseModel
{
    use UserRelationTrait, CompanyTrait, BranchTrait;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message'
    ];

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}

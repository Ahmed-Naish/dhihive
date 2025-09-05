<?php

namespace App\Models\Hrm\Team;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Team extends BaseModel
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [
        'name',
        'team_lead_id',
        'status_id',
        'user_id',
        'company_id',
    ];

    public function teams()
    {
        return $this->hasMany(TeamMember::class);
    }

    // belongs to
    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

}

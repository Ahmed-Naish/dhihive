<?php

namespace App\Models\coreApp\Social;

use App\Models\User;
use App\Models\coreApp\BaseModel;

class SocialIdentity extends BaseModel
{
    protected $fillable = ['user_id', 'provider_name', 'provider_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

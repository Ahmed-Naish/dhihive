<?php

namespace App\Models\Management;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use App\Models\Management\Discussion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscussionLike extends BaseModel
{
    use HasFactory;
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

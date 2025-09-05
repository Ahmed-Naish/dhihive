<?php

namespace App\Models\Management;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscussionComment extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function childComments()
    {
        return $this->hasMany(DiscussionComment::class, 'comment_id')->with('user');
    }
}

<?php

namespace App\Models\TaskManagement;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskDiscussionComment extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function childComments()
    {
        return $this->hasMany(TaskDiscussionComment::class, 'comment_id')->with('user');
    }
}

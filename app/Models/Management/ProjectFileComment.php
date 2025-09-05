<?php

namespace App\Models\Management;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectFileComment extends BaseModel
{
    use HasFactory;

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function childComments()
    {
        return $this->hasMany(ProjectFileComment::class, 'comment_id')->with('user');
    }
}

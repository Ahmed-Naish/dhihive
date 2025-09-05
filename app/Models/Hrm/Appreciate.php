<?php

namespace App\Models\Hrm;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appreciate extends BaseModel
{
    use HasFactory;

    public function appreciateFrom()
    {
        return $this->belongsTo(User::class, 'appreciate_by','id');
    }
}

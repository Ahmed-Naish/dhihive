<?php

namespace App\Models\UserDevice;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDevice extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_name',
        'device_token',
    ];
}

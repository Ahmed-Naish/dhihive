<?php

namespace App\Models\coreApp\User;

use App\Models\coreApp\BaseModel;

class UserProfile extends BaseModel
{
    protected $fillable = ['user_id', 'gender', 'phone_number', 'address', 'date_of_birth'];

}

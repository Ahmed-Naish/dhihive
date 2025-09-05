<?php

namespace App\Models;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Translation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'default',
        'en',
        'bn',
    ];
}

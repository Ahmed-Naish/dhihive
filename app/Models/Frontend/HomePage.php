<?php

namespace App\Models\Frontend;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomePage extends BaseModel
{
    protected $casts = [
        'contents' => 'json'
    ];
    use HasFactory;
}

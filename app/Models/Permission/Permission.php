<?php

namespace App\Models\Permission;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends BaseModel
{
    use HasFactory;

    protected $fillable = ['attributes', 'keywords'];

    protected $casts = [
        'keywords' => 'array'
    ];
}

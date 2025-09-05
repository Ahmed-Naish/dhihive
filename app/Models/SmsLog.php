<?php

namespace App\Models;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsLog extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'id', 'receiver_number', 'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'receiver_number', 'phone');
    }
}

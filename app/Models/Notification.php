<?php

namespace App\Models;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends BaseModel
{
    use HasFactory;
    protected $casts = [
        'seen_at' => 'datetime',
    ];
    protected $table = 'jm_notifications';
    public function receiver()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }
    public static function sender($sender_id)
    {
        return User::find($sender_id);
    }
}

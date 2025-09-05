<?php

namespace App\Models\Notification;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationOld extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id', 'type', 'title', 'message', 'image_id', 'read_at'
    ];

      // sender name belongsTo
      public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
      {
          return $this->belongsTo(User::class, 'sender_id');
      }
      public function receiver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
      {
          return $this->belongsTo(User::class, 'receiver_id');
      }
}

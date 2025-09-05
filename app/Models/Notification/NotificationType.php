<?php

namespace App\Models\Notification;

use App\Models\Upload;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationType extends BaseModel
{
    use HasFactory;


    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }
}

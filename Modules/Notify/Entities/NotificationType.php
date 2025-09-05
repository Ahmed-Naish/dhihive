<?php
namespace Modules\Notify\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Notify\Entities\MessageTemplate;
use Modules\Notify\Entities\UserPreference;

class NotificationType extends Model
{
    use HasFactory;

    protected $table = 'jm_notification_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function templates()
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function preferences()
    {
        return $this->hasMany(UserPreference::class);
    }
}

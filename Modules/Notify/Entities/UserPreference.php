<?php

namespace Modules\Notify\Entities;

use App\Models\NotifyUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Notify\Entities\NotificationType;

class UserPreference extends Model
{
    use HasFactory;
    protected $table = 'jm_user_preferences';
    protected $fillable = [
        'user_id',
        'notification_type_id',
        'preference',
    ];

    public function user()
    {
        return $this->belongsTo(NotifyUser::class);
    }

    public function type()
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }

/**
 * Boot the model.
 */
    protected static function boot()
    {
        parent::boot();

        if (!app()->runningInConsole()) {
            // Set company_id and branch_id before creating a new record
            static::creating(function ($m) {
                if (!isset($m->company_id)) {
                    $m->company_id = Auth::user()->company_id;
                }
                if (!isset($m->branch_id)) {
                    $m->branch_id = Auth::user()->branch_id;
                }
            });

            // Set company_id and branch_id before updating the record
            static::updating(function ($m) {
                if (!isset($m->company_id)) {
                    $m->company_id = Auth::user()->company_id;
                }
                if (!isset($m->branch_id)) {
                    $m->branch_id = Auth::user()->branch_id;
                }
            });
        } else {
            // During migrations or seeding, set default values
            static::creating(function ($m) {
                $m->company_id = 1; // Set your default company_id here
                $m->branch_id = 1; // Set your default branch_id here
            });
        }
    }
}

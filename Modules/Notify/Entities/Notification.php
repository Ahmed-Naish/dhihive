<?php

namespace Modules\Notify\Entities;

use App\Models\User;
use App\Models\NotifyUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Modules\Notify\Entities\NotificationLog;
use Modules\Notify\Entities\NotificationType;
use Modules\Notify\Entities\NotificationReceipt;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'jm_notifications';

    protected $guarded = [];

    protected $casts = [
        'seen_at' => 'datetime',
    ];
    /**
     * Define the relationship with User (assuming it's NotifyUser).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(NotifyUser::class, 'user_id');
    }

    /**
     * Define the relationship with NotificationType.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(NotificationType::class, 'notification_type_id');
    }

    /**
     * Define the relationship with NotificationLog.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    /**
     * Define the relationship with NotificationReceipt.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receipts()
    {
        return $this->hasMany(NotificationReceipt::class);
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

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}

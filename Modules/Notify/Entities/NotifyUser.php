<?php

namespace App\Models;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Notify\Entities\Notification;
use Modules\Notify\Entities\NotificationReceipt;
use Modules\Notify\Entities\UserPreference;
use Spatie\Activitylog\Traits\LogsActivity;

class NotifyUser extends User
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, StatusRelationTrait, LogsActivity, Billable;
    public function preferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

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

}

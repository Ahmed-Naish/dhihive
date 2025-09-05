<?php

namespace Modules\Notify\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Notify\Entities\NotificationType;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $table = 'jm_message_templates';

    protected $fillable = [
        'notification_type_id',
        'subject',
        'body',
    ];

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

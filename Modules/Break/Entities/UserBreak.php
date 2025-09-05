<?php

namespace Modules\Break\Entities;

use App\Models\User;
use Modules\Break\Entities\BreakType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'break_type_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'reason',
        'remark',
        'created_at',
        'updated_at',
        'company_id',
        'branch_id',
        'created_by',
        'updated_by'
    ];

    // break_type_id
    public function breakType()
    {
        return $this->belongsTo(BreakType::class);
    }
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}

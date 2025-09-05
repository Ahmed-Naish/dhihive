<?php

namespace Modules\Break\Entities;

use App\Models\User;
use App\Models\Branch;
use App\Models\Upload;
use App\Models\Company\Company;
use App\Models\coreApp\Status\Status;
use Modules\Break\Entities\UserBreak;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status_id',
        'limit',
        'limit_type',
        'duration_type',
        'max_duration',
        'company_id',
        'branch_id',
        'will_ask_next_meal',
        'icon_id',
    ];

    public function breaks()
    {
        return $this->hasMany(UserBreak::class, 'break_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(Upload::class, 'icon_id');
    }
}

<?php

namespace App\Models\Travel;

use App\Models\Travel\Travel;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\coreApp\Status\Status;
use Modules\Travel\Entities\TravelPlan;
use Modules\Travel\Entities\TravelExpense;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class TravelType extends BaseModel
{
    use HasFactory,CompanyTrait,StatusRelationTrait,BranchTrait;
    protected $fillable = [
        'name',
        'status_id',
        'created_by',
        'updated_by',
        'company_id',
        'branch_id',
    ];
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
    //travels 
    public function travels()
    {
        return $this->hasMany(Travel::class);
    }

    public function travelPlans()
    {
        return $this->hasMany(TravelPlan::class);
    }

    public function travelExpenses()
    {
        return $this->hasMany(TravelExpense::class);
    }
}

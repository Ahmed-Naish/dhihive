<?php


namespace Modules\Saas\Entities;

use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingPlanPrice extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function status()
    {
        return $this->belongsTo(Status::class,'status_id','id');
    }

    public function pricingPlan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'pricing_plan_id', 'id');
    }

    public function planDurationType(): BelongsTo
    {
        return $this->belongsTo(PlanDurationType::class, 'plan_duration_type_id', 'id');
    }
}

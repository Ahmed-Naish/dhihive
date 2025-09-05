<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanDurationType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function pricingPlanPrices(): HasMany
    {
        // Define the relationship with PricingPlanPrice, specifying foreign keys.
        return $this->hasMany(PricingPlanPrice::class, 'plan_duration_type_id', 'id')
            ->with('pricingPlan.pricingPlanFeatures'); // Eager load related models.
    }
}

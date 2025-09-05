<?php


namespace Modules\Saas\Entities;

use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingPlan extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function status()
    {
        return $this->belongsTo(Status::class,'status_id','id');
    }

    public function pricingPlanPrices(): HasMany
    {
        return $this->hasMany(PricingPlanPrice::class, 'pricing_plan_id', 'id');
    }

    public function pricingPlanFeatures(): HasMany
    {
        return $this->hasMany(PricingPlanFeature::class, 'pricing_plan_id', 'id');
    }
}

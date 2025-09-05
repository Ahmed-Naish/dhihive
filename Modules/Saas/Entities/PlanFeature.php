<?php


namespace Modules\Saas\Entities;

use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function status()
    {
        return $this->belongsTo(Status::class,'status_id','id');
    }

    public function pricingPlanFeatures()
    {
        return $this->hasMany(PricingPlanFeature::class, 'plan_feature_id', 'id');
    }
}

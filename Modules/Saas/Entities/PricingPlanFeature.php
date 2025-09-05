<?php


namespace Modules\Saas\Entities;

use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingPlanFeature extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function planFeature(): BelongsTo
    {
        return $this->belongsTo(PlanFeature::class, 'plan_feature_id', 'id');
    }
}

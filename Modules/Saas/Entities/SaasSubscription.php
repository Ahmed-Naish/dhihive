<?php

namespace Modules\Saas\Entities;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class SaasSubscription extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $table = 'saas_subscriptions';

    protected $guarded = [];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function pricingPlanPrice(): BelongsTo
    {
        return $this->belongsTo(PricingPlanPrice::class, 'pricing_plan_price_id', 'id');
    }

    public function scopeProcessingForApprove($query)
    {
        $query->where('is_processing_for_approve', 1);
    }

    public function scopeApproved($query)
    {
        $query->where('status_id', 5);
    }

    public function scopeRunningSubscription($query)
    {
        $query->where('is_running_subscription', 1);
    }
}

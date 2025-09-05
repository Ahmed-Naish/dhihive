<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionPackage;
use Modules\Saas\Entities\SubscriptionProductPlan;

class SubscriptionPackagePlan extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPackagePlanFactory::new();
    }
    public function plan()
    {
        return $this->belongsTo(SubscriptionProductPlan::class, 'subscription_plan_id');
    }
    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }
}

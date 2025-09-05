<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Saas\Entities\SubscriptionModule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionPackage;

class SubscriptionPackageDetails extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPackageDetailsFactory::new();
    }

    public function module()
    {
        return $this->belongsTo(SubscriptionModule::class, 'subscription_module_id');
    }
    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }
}

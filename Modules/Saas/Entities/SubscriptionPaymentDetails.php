<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionCurrency;
use Modules\Saas\Entities\SubscriptionPurchasedPackage;

class SubscriptionPaymentDetails extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPaymentDetailsFactory::new();
    }
    public function currency()
    {
        return $this->belongsTo(SubscriptionCurrency::class, 'currency_id');
    }
    public function package()
    {
        return $this->belongsTo(SubscriptionPurchasedPackage::class, 'purchased_package_id');
    }
}

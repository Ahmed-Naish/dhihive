<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionPayoutMethod;

class SubscriptionPayout extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPayoutFactory::new();
    }

    public function currency()
    {
        return $this->belongsTo(SubscriptionCurrency::class, 'currency_id');
    }
    public function payoutMethod()
    {
        return $this->belongsTo(SubscriptionPayoutMethod::class, 'subscription_payout_method_id');
    }
}

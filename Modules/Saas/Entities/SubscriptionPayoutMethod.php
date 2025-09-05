<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionCurrency;

class SubscriptionPayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPayoutMethodFactory::new();
    }
    public function currency()
    {
        return $this->belongsTo(SubscriptionCurrency::class, 'subscription_currency_id');
    }
   
}

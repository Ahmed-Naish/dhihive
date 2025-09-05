<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionCurrency;
use Modules\Saas\Entities\SubscriptionPackagePlan;
use Modules\Saas\Entities\SubscriptionPaymentMethod;

class SubscriptionPurchasedPackage extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPurchasedPackageFactory::new();
    }
    public function packagePlan()
    {
        return $this->belongsTo(SubscriptionPackagePlan::class, 'package_plan_id');
    }
    public function paymentMethod()
    {
        return $this->belongsTo(SubscriptionPaymentMethod::class, 'payment_method_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function currency()
    {
        return $this->belongsTo(SubscriptionCurrency::class, 'currency_id');
    }    
    public function PaymentDetails()
    {
        return $this->belongsTo(SubscriptionPaymentDetails::class, 'currency_id');
    }
}

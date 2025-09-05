<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Saas\Entities\SubscriptionModule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionModuleTerms;
use Modules\Saas\Entities\SubscriptionModuleValue;

class SubscriptionModuleDetails extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionModuleAssignFactory::new();
    }
    public function module()
    {
        return $this->belongsTo(SubscriptionModule::class, 'module_id');
    }
    public function moduleTerm()
    {
        return $this->belongsTo(SubscriptionModuleTerms::class, 'module_term_id');
    }
    public function moduleValue()
    {
        return $this->belongsTo(SubscriptionModuleValue::class, 'module_value_id');
    }
    public function product()
    {
        return $this->belongsTo(SubscriptionProduct::class, 'product_id');
    }
}

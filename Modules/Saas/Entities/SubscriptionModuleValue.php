<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionProduct;
use Modules\Saas\Entities\SubscriptionModuleCategory;

class SubscriptionModuleValue extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionModuleValueFactory::new();
    }
    public function category()
    {
        return $this->belongsTo(SubscriptionModuleCategory::class, 'category_id');
    }
    public function product()
    {
        return $this->belongsTo(SubscriptionProduct::class, 'product_id');
    }
}

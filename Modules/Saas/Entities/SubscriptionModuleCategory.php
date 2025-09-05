<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionProduct;

class SubscriptionModuleCategory extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionModuleCategoryFactory::new();
    }
    public function product()
    {
        return $this->belongsTo(SubscriptionProduct::class, 'subscription_product_id');
    }
    
}

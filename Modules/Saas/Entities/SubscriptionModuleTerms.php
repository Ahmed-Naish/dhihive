<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionProduct;
use Modules\Saas\Entities\SubscriptionModuleCategory;

class SubscriptionModuleTerms extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionModuleTermsFactory::new();
    }

    public function moduleCategory()
    {
        return $this->belongsTo(SubscriptionModuleCategory::class, 'category_id');
    }
    public function product()
    {
        return $this->belongsTo(SubscriptionProduct::class, 'product_id');
    }

}

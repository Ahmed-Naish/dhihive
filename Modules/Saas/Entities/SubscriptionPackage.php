<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionCurrency;

class SubscriptionPackage extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPackageFactory::new();
    }
    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }
    public function currency()
    {
        return $this->belongsTo(SubscriptionCurrency::class, 'currency_id');
    }
    public function product()
    {
        return $this->belongsTo(SubscriptionProduct::class, 'product_id');
    }
}

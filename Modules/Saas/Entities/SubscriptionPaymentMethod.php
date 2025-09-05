<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionCurrency;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class SubscriptionPaymentMethod extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionPaymentMethodFactory::new ();
    }
    public function currency()
    {
        return $this->belongsTo(SubscriptionCurrency::class, 'currency_id');
    }

}

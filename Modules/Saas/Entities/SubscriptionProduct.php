<?php

namespace Modules\Saas\Entities;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionCurrency;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class SubscriptionProduct extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];

    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }

   
}

<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionModule extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    protected $casts = [
        'term_wise_price' => 'array',
    ];

    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }

    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionModuleFactory::new ();
    }
}

<?php

namespace Modules\Saas\Entities;

use Spatie\Activitylog\LogOptions;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class SubscriptionCurrency extends Model
{

    use HasFactory, StatusRelationTrait, LogsActivity;

    protected $fillable = [
        'id', 'title', 'code', 'symbol', 'exchange_rate', 'position', 'precision',
    ];

    protected static $logAttributes = [
        'id', 'title', 'code', 'symbol', 'exchange_rate', 'position', 'precision',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\SubscriptionCurrencyFactory::new ();
    }

}

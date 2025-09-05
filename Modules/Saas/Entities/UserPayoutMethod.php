<?php

namespace Modules\Saas\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Saas\Entities\SubscriptionPayoutMethod;

class UserPayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Saas\Database\factories\UserPayoutMethodFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function payoutMethod()
    {
        return $this->belongsTo(SubscriptionPayoutMethod::class, 'payout_method_id');
    }
}

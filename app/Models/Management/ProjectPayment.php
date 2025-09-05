<?php

namespace App\Models\Management;

use App\Models\coreApp\BaseModel;
use App\Models\Expenses\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectPayment extends BaseModel
{
    use HasFactory;

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}

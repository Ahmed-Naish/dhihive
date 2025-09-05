<?php

namespace App\Models\Expenses;

use App\Models\coreApp\BaseModel;
use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentHistory extends BaseModel
{
    use HasFactory;

    public function paymentStatus()
    {
        return $this->belongsTo(Status::class, 'payment_status_id');
    }

}

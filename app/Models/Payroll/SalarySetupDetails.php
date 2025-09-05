<?php

namespace App\Models\Payroll;

use App\Models\coreApp\BaseModel;
use App\Models\Payroll\Commission;
use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalarySetupDetails extends BaseModel
{
    use HasFactory;

    function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}

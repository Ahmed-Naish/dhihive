<?php

namespace App\Models\Expenses;

use App\Models\Upload;
use App\Models\Company\Company;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends BaseModel
{
    use HasFactory, SoftDeletes,BranchTrait, CompanyTrait;

    protected $table = 'payment_methods';

    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    public function attachments():BelongsTo
    {
        return $this->belongsTo(Upload::class,'attachment_file_id','id');
    }

    public function status():BelongsTo
    {
        return $this->belongsTo(Status::class,'status_id','id');
    }
}

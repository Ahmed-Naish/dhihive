<?php

namespace App\Models\Hrm\Notice;

use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeViewLog extends BaseModel
{
    use HasFactory, CompanyTrait, BranchTrait;

    protected $fillable = [
        'company_id',
        'user_id',
        'notice_id',
        'is_view',
    ];
}

<?php

namespace App\Models\Management;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectMembar extends BaseModel
{
    use HasFactory, BranchTrait, CompanyTrait;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function _by()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}

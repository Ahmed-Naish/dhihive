<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\coreApp\Status\Status;

class EmailTemplate extends Model
{
    use HasFactory;

    public function status()
    {
        return $this->belongsTo(Status::class,'status_id','id');
    }
}

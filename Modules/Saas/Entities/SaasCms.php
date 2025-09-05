<?php

namespace Modules\Saas\Entities;

use App\Models\coreApp\Status\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaasCms extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}

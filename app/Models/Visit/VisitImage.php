<?php

namespace App\Models\Visit;

use App\Models\Upload;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitImage extends BaseModel
{
    use HasFactory;

    public function imageable()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->belongsTo(Upload::class, 'file_id');
    }
}

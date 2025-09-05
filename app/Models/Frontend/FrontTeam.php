<?php

namespace App\Models\Frontend;

use App\Models\Upload;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class FrontTeam extends BaseModel
{
    use HasFactory,StatusRelationTrait;

    public function image()
    {
        return $this->belongsTo(Upload::class, 'attachment', 'id');
    }
}

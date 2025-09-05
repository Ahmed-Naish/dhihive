<?php

namespace App\Models\Frontend;

use App\Models\coreApp\BaseModel;
use App\Models\Content\AllContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Menu extends BaseModel
{

    use HasFactory,StatusRelationTrait;


    public function page()
    {
        return $this->belongsTo(AllContent::class, 'all_content_id', 'id');
    }
}

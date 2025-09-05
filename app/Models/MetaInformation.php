<?php

namespace App\Models;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetaInformation extends BaseModel
{
    use HasFactory;


    protected $fillable = ['type', 'meta_title', 'meta_image', 'meta_description', 'meta_keywords', 'created_by', 'updated_by'];

    protected static $logAttributes = ['type', 'meta_title', 'meta_image', 'meta_description', 'meta_keywords', 'created_by', 'updated_by'];


}

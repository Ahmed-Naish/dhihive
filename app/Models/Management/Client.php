<?php

namespace App\Models\Management;

use App\Models\Visit\VisitImage;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Hrm\Country\Country;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class Client extends BaseModel
{
    use HasFactory,StatusRelationTrait, SoftDeletes,BranchTrait, CompanyTrait;

    public function avater()
    {
        return $this->morphOne(VisitImage::class, 'imageable');
    }

    //country relation
    public function countryInfo()
    {
        return $this->belongsTo(Country::class,'country');
    } 
}

<?php

namespace App\Models\Track;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use App\Models\Traits\BranchTrait;
use App\Models\Traits\CompanyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationLog extends BaseModel
{
    use HasFactory,BranchTrait,CompanyTrait;

    protected $fillable = ['company_id', 'user_id', 'date', 'location', 'latitude', 'longitude','speed','heading','city','address','countryCode','country','distance'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

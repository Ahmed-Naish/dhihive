<?php

namespace App\Models\Hrm\Country;

use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends BaseModel
{
    use HasFactory;
    protected $fillable = ['country_code', 'name', 'time_zone', 'currency_code', 'currency_symbol', 'currency_name', 'currency_symbol_placement'];
}

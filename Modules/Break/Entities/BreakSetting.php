<?php

namespace Modules\Break\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo('Modules\Company\Entities\Company');
    }

    public function branch()
    {
        return $this->belongsTo('Modules\Company\Entities\Branch');
    }
}

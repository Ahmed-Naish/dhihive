<?php

namespace App\Models;

use App\Models\User;
use App\Models\Slider;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Upload extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function sliders(): HasOne
    {
        return $this->hasOne(Slider::class);
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pages(): HasOne
    {
        return $this->hasOne(Page::class);
    }

    public function advertise(): HasOne
    {
        return $this->hasOne(Advertise::class);
    }
}

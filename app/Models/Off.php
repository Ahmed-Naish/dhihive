<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Off extends Model
{
    protected $table = 'off';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'em_id',
        'day',
    ];
}

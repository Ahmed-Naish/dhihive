<?php

namespace Modules\Services\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceInstitution extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Services\Database\factories\ServiceInstitutionFactory::new();
    }
}

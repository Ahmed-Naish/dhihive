<?php

namespace Modules\Services\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Services\Entities\ServiceBrand;
use Modules\Services\Entities\ServiceModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class ServiceMachine extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Services\Database\factories\ServiceMachineFactory::new();
    }
    public function brand()
    {
        return $this->belongsTo(ServiceBrand::class, 'brand_id');
    }    
    public function model()
    {
        return $this->belongsTo(ServiceModel::class, 'model_id');
    }
}

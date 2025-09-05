<?php

namespace Modules\Services\Entities;

use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Modules\Services\Entities\ServiceBrand;
use Modules\Services\Entities\ServiceModel;
use Modules\Services\Entities\ServiceMachine;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServicePackageDetail extends Model
{
    use HasFactory,StatusRelationTrait;

    protected $guarded = [
       'id', 'created_at', 'updated_at'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Services\Database\factories\ServicePackageDetailFactory::new();
    }
    public function brand()
    {
        return $this->belongsTo(ServiceBrand::class, 'brand_id');
    }
    public function model()
    {
        return $this->belongsTo(ServiceModel::class, 'model_id');
    }
    public function machine()
    {
        return $this->belongsTo(ServiceMachine::class, 'machine_id');
    }     
    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    } 
}

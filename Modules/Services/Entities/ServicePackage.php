<?php

namespace Modules\Services\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Services\Entities\ServiceBrand;
use Modules\Services\Entities\ServiceModel;
use Modules\Services\Entities\ServiceMachine;
use Modules\Services\Entities\ServicePackageDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class ServicePackage extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [
        'name'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Services\Database\factories\ServicePackageFactory::new();
    }   
    public function packageDetails()
    {
        return $this->hasMany(ServicePackageDetail::class, 'machine_id');
    }
}

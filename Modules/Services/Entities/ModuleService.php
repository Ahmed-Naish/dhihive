<?php

namespace Modules\Services\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Services\Entities\ServiceMachine;
use Modules\Services\Entities\ServicePackage;
use Modules\Services\Entities\ServiceInstitution;
use Modules\Services\Entities\ModuleServiceDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class ModuleService extends Model
{
    use HasFactory, StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Services\Database\factories\ServiceFactory::new();
    }
    public function institution()
    {
        return $this->belongsTo(ServiceInstitution::class, 'institution_id');
    }    
    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }     
    public function packageDetails()
    {
        return $this->belongsTo(ServicePackageDetail::class, 'package_id');
    }
    public function serviceDetails()
    {
        return $this->hasMany(ModuleServiceDetail::class, 'module_service_id');
    }

}

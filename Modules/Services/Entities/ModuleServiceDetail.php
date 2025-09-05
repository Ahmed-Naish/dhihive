<?php

namespace Modules\Services\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Services\Entities\ModuleService;
use Modules\Services\Entities\ServiceMachine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class ModuleServiceDetail extends Model
{
    use HasFactory,StatusRelationTrait;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Services\Database\factories\ModuleServiceDetailFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'contract_person_id');
    }
    public function machine()
    {
        return $this->belongsTo(ServiceMachine::class, 'machine_id');
    }      
    public function service()
    {
        return $this->belongsTo(ModuleService::class, 'module_service_id');
    }   
}

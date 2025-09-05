<?php
namespace App\Models\Traits;

use App\Models\Branch;
use App\Scopes\BranchScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait BranchTrait
{

    protected static function bootBranchTrait()
    {
        if(isModuleActive('MultiBranch')){
            static::addGlobalScope(new BranchScope);
            static::creating(function ($model) {
                if (Schema::hasColumn($model->getTable(), 'branch_id'))
                $model->branch_id = userBranch();
            });
        }
    }

    public function branch_info()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}

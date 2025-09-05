<?php
  
namespace App\Scopes;
  
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
  
class BranchScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check() && Schema::hasColumn($model->getTable(), 'branch_id')) {
            $builder->where($model->getTable().'.branch_id', '=', userBranch());   
        }
    }
}
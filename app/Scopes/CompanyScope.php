<?php
  
namespace App\Scopes;
  
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class CompanyScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check() && Schema::hasColumn($model->getTable(), 'company_id')) {
            $builder->where($model->getTable().'.company_id', '=', auth()->user()->company_id);   
        } elseif(
            config('app.single_db') 
            && !in_array(url('/'), config('tenancy.central_domains')) 
            && config('app.mood') === 'Saas' && isModuleActive('Saas') 
            && getCurrentDomainCompany()
        ) {
            $builder->where($model->getTable().'.company_id', '=', getCurrentDomainCompany()->id);
        }
    }
}
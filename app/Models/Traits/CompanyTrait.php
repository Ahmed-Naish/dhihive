<?php
namespace App\Models\Traits;

use App\Scopes\CompanyScope;
use App\Models\Company\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait CompanyTrait
{

    protected static function bootCompanyTrait()
    {
        static::addGlobalScope(new CompanyScope);

        if (Auth::check()) {
            static::creating(function ($model) {
                if (Schema::hasColumn($model->getTable(), 'company_id'))
                $model->company_id = auth()->user()->company->id;
            });
        }
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}

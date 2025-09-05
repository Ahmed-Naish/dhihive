<?php

namespace App\Models\coreApp;

use Spatie\Activitylog\LogOptions;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }


    // protected static function boot()
    // {
    //     parent::boot();

    //     $tenant = (!in_array(url('/'), config('tenancy.central_domains')) && config('app.mood') === 'Saas' && isModuleActive('Saas'));
    //     $regular = config('app.mood') !== 'Saas' && !isModuleActive('Saas') ? true : false;

    //     if (!app()->runningInConsole() && ($tenant || $regular)) {

    //         static::creating(function ($model) {
                
    //             $data = [];

    //             if (Schema::hasColumn($model->getTable(), 'company_id')) {
    //                 $data['company_id'] = auth()->user()->company_id;
    //             }

    //             if (Schema::hasColumn($model->getTable(), 'branch_id')) {
    //                 $data['branch_id'] = auth()->user()->branch_id;
    //             }

    //             if (count($data) > 0) {
    //                 $model->fill($data);
    //             }
    //         });
    //     }
    // }

    public function scopeAuthorizable($query)
    {
        if (config('app.single_db')) {
            $query->when(Schema::hasColumn($this->getTable(), 'company_id'), function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            })->when(Schema::hasColumn($this->getTable(), 'branch_id'), function ($q) {
                $q->where('branch_id', auth()->user()->branch_id);
            });
        }

        return $query;
    }
}

@extends('backend.layouts.app')
@section('title', @$data['title'])
@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}

    <div class="row ">
        <div class="col-md-4 mb-3">
            <div class="table-content table-basic ">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive  min-height-500">
                            <table class="table table-bordered" id="table">
                                <thead class="thead">
                                    <tr>
                                        <td colspan="2">
                                            <div class="d-flex align-items-center justify-content-between gap-2 py-2">
                                                <h2 class="fs-2 mb-0">{{ $data['pricingPlan']->name }}</h2>
                                                <span class="bg-primary py-1 px-3 rounded-4 text-white">{{ _trans('common.Most Popular') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ _trans('common.Employee Limit') }}</td>
                                        <td class="text-center">{{ $data['pricingPlan']->is_employee_limit ? $data['pricingPlan']->employee_limit : _trans('common.Unlimited') }}</td>
                                    </tr>
                                    @foreach ($data['planFeatures'] as $id => $title)
                                        <tr>
                                            <td>{{ $title }}</td>
                                            <td width="50px" class="text-center">
                                                @if (in_array($id, $data['pricingPlanFeatures']))
                                                    <i class="fa-solid fa-check text-primary"></i>
                                                @else
                                                    <i class="fa-solid fa-times text-danger"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="table-content table-basic ">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive  min-height-500">
                            <table class="table table-bordered" id="table">
                                <thead class="thead">
                                    <tr>
                                        <td colspan="3">
                                            <h2 class="fs-2 mb-0 py-2">{{ _trans('common.Prices') }}</h2>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['pricingPlan']->pricingPlanPrices as $pricingPlanPrice)
                                        <tr>
                                            <td>{{ $pricingPlanPrice->planDurationType->name }}</td>
                                            <td width="50px" class="text-center">
                                                {{ currency_format(number_format($pricingPlanPrice->price, 2)) }}
                                            </td>
                                            <td width="80px" class="text-center">
                                                @if ($pricingPlanPrice->status_id == 1)
                                                    <span class="text-{{ $pricingPlanPrice->status->class }}">{{ _trans('common.Active') }}</span>
                                                @else
                                                    <span class="text-{{ $pricingPlanPrice->status->class }}">{{ _trans('common.Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
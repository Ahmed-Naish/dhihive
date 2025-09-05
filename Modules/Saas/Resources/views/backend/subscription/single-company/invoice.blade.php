@extends('backend.layouts.app')
@section('title', @$title)
@section('styles')
    <style>
        #invoice {
            width: 796px;
            min-height: 500px;
            max-height: 1123px;
            margin: 0 auto;
        }
    </style>
@endsection
@section('content')
    {!! breadcrumb([
        'title' => @$title,
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$title,
    ]) !!}
    <div class="container">
        <div id="invoice">
            <div class="card border-0 mb-5">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6 col-md-4">
                            <h3 class="mb-1 fw-bold">#{{ $subscription->invoice_no }}</h3>
                            <div>Date: {{ showDate($subscription->created_at) }}</div>
                            <div>Payment Gateway: {{ $subscription->is_demo_checkout ? 'Demo Checkout' : $subscription->payment_gateway }}</div>
                            <div>Payment Status: <span class="fw-bold text-{{ $subscription->paymentStatus->class }}">{{ $subscription->paymentStatus->name }}</span></div>
                            <div>Status: <span class="fw-bold text-{{ $subscription->status->class }}">{{ $subscription->status->name }}</span></div>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>Plan Info</th>
                                    <th>Employee Limit</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Price</th>
                                </tr>
                                <tr>
                                    <td>{{ (config('app.single_db')) ?  @$subscription->pricingPlanPrice->pricingPlan->name : $subscription->plan_name }}</td>
                                    <td>{{ $subscription->employee_limit }}</td>
                                    <td>{{ showDate($subscription->expiry_date) }}</td>
                                    <td><span class="fw-bold text-{{ $subscription->status->class }}">{{ $subscription->status->name }}</span></td>
                                    <td class="text-end">{{ currency_format(number_format($subscription->price, 2)) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-lg-2 col-sm-5">
                            <table class="table table-borderless table-striped">
                                <tr>
                                    <td class="left">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="text-end">{{ currency_format(number_format($subscription->price, 2)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @include('backend.partials.table_js')
@endsection

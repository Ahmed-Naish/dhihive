@extends('saas::frontend.layouts.master')

@section('title', _trans('frontend.Subscription Invoice'))

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
    <div class="container">
        <div id="invoice">
            <div class="alert alert-success fw-bold">
                {{ _trans('frontend.Your order has been successfully placed, and our dedicated team will meticulously review and approve your request within a timeframe ranging from 24 to 72 hours') }}
            </div>
            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6 col-md-4">
                            <h6 class="mb-3">{{ _trans('frontend.From') }}:</h6>
                            <div>
                                <strong>{{ $mainCompany->name }}</strong>
                            </div>
                            <div>{{ $mainCompany->company_name }}</div>
                            <div>{{ $mainCompany->country->name }}</div>
                            <div>{{ _trans('frontend.Email') }}: {{ $mainCompany->email }}</div>
                            <div>{{ _trans('frontend.Phone') }}: {{ $mainCompany->phone }}</div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <h6 class="mb-3">{{ _trans('frontend.To') }}:</h6>
                            <div>
                                <strong>{{ $subscription->company->name }}</strong>
                            </div>
                            <div>{{ $subscription->company->company_name }}</div>
                            <div>{{ $subscription->company->country->name }}</div>
                            <div>{{ _trans('frontend.Email') }}: {{ $subscription->company->email }}</div>
                            <div>{{ _trans('frontend.Phone') }}: {{ $subscription->company->phone }}</div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <h3 class="mb-1 fw-bold">#{{ $subscription->invoice_no }}</h3>
                            <div>{{ _trans('frontend.Date') }}: {{ showDate($subscription->created_at) }}</div>
                            <div>{{ _trans('frontend.Expiry Date') }}: {{ showDate($subscription->expiry_date) }}</div>
                            <div>{{ _trans('frontend.Payment Gateway') }}: {{ $subscription->is_demo_checkout ? _trans('frontend.Demo Checkout') : $subscription->payment_gateway }}</div>
                            <div>{{ _trans('frontend.Payment Status') }}: <span class="fw-bold text-{{ $subscription->paymentStatus->class }}">{{ $subscription->paymentStatus->name }}</span></div>
                            <div>{{ _trans('frontend.Status') }}: <span class="fw-bold text-{{ $subscription->status->class }}">{{ $subscription->status->name }}</span></div>
                        </div>
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>{{ _trans('frontend.Plan Info') }}</th>
                                    <th>{{ _trans('frontend.Employee Limit') }}</th>
                                    <th>{{ _trans('frontend.Expiry Date') }}</th>
                                    <th>{{ _trans('frontend.Status') }}</th>
                                    <th class="text-end">{{ _trans('frontend.Price') }}</th>
                                </tr>
                                <tr>
                                    <td>{{ $subscription->pricingPlanPrice->pricingPlan->name }}</td>
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
                                        <strong>{{ _trans('frontend.Total') }}</strong>
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
@extends('backend.layouts.app')
@section('title', @$data['title'])

@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}
    <div class="table-content table-basic">
        <div class="card">
            <div class="card-body">
                <div
                    class="table-toolbar d-flex flex-wrap gap-2 flex-xl-row justify-content-center justify-content-xxl-between align-content-center pb-3">
                    <div class="align-self-center">
                        <div class="d-flex flex-wrap gap-2 flex-lg-row justify-content-center align-content-center">
                            <form class="d-flex align-items-center gap-2">
                                <div class="search-box">
                                    <input class="form-control" placeholder="{{ _trans('common.Search') }}" name="search" value="{{ request('search') }}" autocomplete="off">
                                </div>
                                <div class="search-box">
                                    <select name="company_id" class="form-control">
                                        <option value="" selected>{{ _trans('common.All Company') }}</option>
                                        @foreach ($data['companies'] ?? [] as $id => $company_name)
                                            <option value="{{ $id }}" {{ request('company_id') == $id ? 'selected' : '' }}>{{ $company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="search-box">
                                    <select name="payment_status_id" class="form-control">
                                        <option value="" selected>All</option>
                                        <option value="8" {{ request('payment_status_id') == 8 ? 'selected' : '' }}>{{ _trans('common.Paid') }}</option>
                                        <option value="9" {{ request('payment_status_id') == 9 ? 'selected' : '' }}>{{ _trans('common.Unpaid') }}</option>
                                    </select>
                                </div>
                                <button class="btn btn-primary py-2"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive  min-height-500">
                    <table class="table table-bordered" id="table">
                        <thead class="thead">
                            <tr>
                                <th>Invoice No</th>
                                <th>Company</th>
                                <th>Amount</th>
                                <th>Payment Gateway</th>
                                <th>Trx No</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            @foreach ($data['transactions'] ?? [] as $transaction)
                                <tr>
                                    <td>{{ $transaction->invoice_no }}</td>
                                    <td>{{ $transaction->company->company_name }}</td>
                                    <td>{{ $transaction->price }}</td>
                                    <td>{{ $transaction->is_demo_checkout ? 'Demo Checkout' : $transaction->payment_gateway }}</td>
                                    <td>{{ $transaction->trx_id }}</td>
                                    <td>{{ $transaction->paymentStatus->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <div class="mt-5">
                        {{ $data['transactions']->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('script')
@endsection

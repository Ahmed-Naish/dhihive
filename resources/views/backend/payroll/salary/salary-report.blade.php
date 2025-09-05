@extends('backend.layouts.app')
@section('title', @$data['title'])

@section('style')
    <style>
        .addition {
            background: #b1f4c0 !important;
            color: #016718 !important;
        }

        .deduction {
            background: #ffc4c4 !important;
            color: #971414 !important;
        }

        .thead-bg {
            background: #efefef !important
        }
    </style>
@endsection

@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}

    <div class="table-content table-basic">
        <div class="card">
            <div class="card-body"> 
                <div class="table-toolbar pb-3">
                    <form class="row">
                        <div class="col-md-4 col-lg-2">
                            <input type="month" name="month" class="form-control ot-form-control ot-input" value="{{ request('month') }}">
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <select name="department_id" class="form-select select2">
                                <option value="0">{{ _trans('common.All Department') }}</option>
                                @foreach (@$data['departments'] ?? [] as $id => $title)
                                    <option value="{{ $id }}" {{ request('department_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <select name="user_id" class="form-select select2">
                                <option value="0">{{ _trans('common.All Employee') }}</option>
                                @foreach (@$data['employees'] ?? [] as $id => $title)
                                    <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <select name="commission_type" class="form-select select2">
                                <option value="0">{{ _trans('common.All Commission') }}</option>
                                <option value="addition" {{ request('commission_type') == 'addition' ? 'selected' : '' }}>{{ _trans('common.Addition') }}</option>
                                <option value="deduction" {{ request('commission_type') == 'deduction' ? 'selected' : '' }}>{{ _trans('common.Deduction') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-2 d-flex gap-3">
                            <button class="btn-add py-3">
                                <i class="fa-solid fa-search"></i>
                                {{ _trans('common.Search') }}
                            </button>
                            <button name="is_export" class="btn btn-outline-dark float-end" value="1">
                                <i class="fa-solid fa-file-excel"></i>
                                {{ _trans('common.Export Excel') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive min-height-500">
                    <table class="table table-bordered" id="table">
                        <thead class="thead">
                            <tr>
                                <th class="thead-bg" rowspan="2">{{ _trans('common.Employee') }}</th>
                                <th class="thead-bg" rowspan="2">{{ _trans('common.Month') }}</th>
                                <th class="text-end thead-bg" rowspan="2">{{ _trans('common.Salary') }}</th>
                                <th class="text-center addition" colspan="{{ count($data['additions']) + 1 }}">{{ _trans('common.Addition') }}</th>
                                <th class="text-center deduction" colspan="{{ count($data['deductions']) + 3 }}">{{ _trans('common.Deduction') }}</th>
                                <th class="text-end thead-bg" rowspan="2">{{ _trans('common.Adjust') }}</th>
                                <th class="text-end thead-bg" rowspan="2">{{ _trans('common.Net Salary') }}</th>
                                <th class="text-end thead-bg" rowspan="2">{{ _trans('common.Paid') }}</th>
                                <th class="text-end thead-bg" rowspan="2">{{ _trans('common.Due') }}</th>
                            </tr>
                            <tr>
                                @foreach ($data['additions'] ?? [] as $addition)
                                    <th class="text-end thead-bg">{{ $addition }}</th>
                                @endforeach
                                <th class="text-end thead-bg">{{ _trans('common.Total') }}</th>
                                @foreach ($data['deductions'] ?? [] as $deduction)
                                    <th class="text-end thead-bg">{{ $deduction }}</th>
                                @endforeach
                                <th class="text-end thead-bg">{{ _trans('common.Advanced') }}</th>
                                <th class="text-end thead-bg">{{ _trans('common.Absent') }}</th>
                                <th class="text-end thead-bg">{{ _trans('common.Total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            @php
                                $thisPageTotalAddition = 0;
                                $thisPageTotalDeduction = 0;
                            @endphp
                            @forelse ($data['salaryGenerates'] ?? [] as $salaryGenerate)
                                <tr>
                                    <td>{{ $salaryGenerate->employee->name }} [{{ $salaryGenerate->employee->employee_id }}]</td>
                                    <td>{{ date('F Y', strtotime($salaryGenerate->date)) }}</td>
                                    <td class="text-end">{{ currency_format(number_format($salaryGenerate->gross_salary, 2)) }}</td>

                                    @php
                                        $additionAssoc = [];
                                        foreach ($salaryGenerate->allowance_details as $item) {
                                            $additionAssoc[$item['name']] = $item['amount'];
                                        }
                                        $totalAddition = 0;
                                    @endphp

                                    @foreach ($data['additions'] ?? [] as $addition)
                                        <td class="text-end">
                                            @php
                                                $amount = $additionAssoc[$addition] ?? 0;
                                                $totalAddition += $amount;
                                                $thisPageTotalAddition += $amount;
                                            @endphp
                                            {{ currency_format(number_format($amount, 2)) }}
                                        </td>
                                    @endforeach

                                    <td>
                                        {{ currency_format(number_format($totalAddition, 2)) }}
                                    </td>

                                    @php
                                        $deductionAssoc = [];
                                        foreach ($salaryGenerate->deduction_details as $item) {
                                            $deductionAssoc[$item['name']] = $item['amount'];
                                        }
                                        $totaldeduction = 0;
                                    @endphp

                                    @foreach ($data['deductions'] ?? [] as $deduction)
                                        <td class="text-end">
                                            @php
                                                $amount = $deductionAssoc[$deduction] ?? 0;
                                                $totaldeduction += $amount;
                                                $thisPageTotalDeduction += $amount;
                                            @endphp
                                            {{ currency_format(number_format($amount, 2)) }}
                                        </td>
                                    @endforeach
                                    
                                    <td class="text-end">
                                        {{ currency_format(number_format($salaryGenerate->advance_amount, 2)) }}
                                    </td>
                                    <td class="text-end">
                                        {{ currency_format(number_format($salaryGenerate->absent_amount, 2)) }}
                                    </td>
                                    <td>{{ currency_format(number_format($totaldeduction, 2)) }}</td>
                                    <td class="text-end">{{ currency_format(number_format($salaryGenerate->adjust, 2)) }}</td>
                                    <td class="text-end">{{ currency_format(number_format($salaryGenerate->net_salary, 2)) }}</td>
                                    <td class="text-end">{{ currency_format(number_format($salaryGenerate->net_salary - $salaryGenerate->due_amount, 2)) }}</td>
                                    <td class="text-end">{{ currency_format(number_format($salaryGenerate->due_amount, 2)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="30" class="text-center fw-bold text-danger fs-6">
                                        {{ _trans('common.No data found!') }}
                                    </td>
                                </tr>
                            @endforelse
                            <tr>
                                <th colspan="3" class="text-end thead-bg">
                                    {{ _trans('common.Total') }}
                                </th>
                                <th colspan="{{ count($data['additions']) + 1 }}" class="text-end thead-bg">
                                    {{ currency_format(number_format($thisPageTotalAddition, 2)) }}
                                </th>
                                <th colspan="{{ count($data['deductions']) + 3 }}" class="text-end thead-bg">
                                    {{ currency_format(number_format($thisPageTotalDeduction, 2)) }}
                                </th>
                                <th class="text-end thead-bg">
                                    {{ currency_format(number_format($data['salaryGenerates']->sum('adjust'), 2)) }}
                                </th>
                                <th class="text-end thead-bg">
                                    {{ currency_format(number_format($data['salaryGenerates']->sum('net_salary'), 2)) }}
                                </th>
                                <th class="text-end thead-bg">
                                    {{ currency_format(number_format($data['salaryGenerates']->sum('net_salary') - $data['salaryGenerates']->sum('due_amount'), 2)) }}
                                </th>
                                <th class="text-end thead-bg">
                                    {{ currency_format(number_format($data['salaryGenerates']->sum('due_amount'), 2)) }}
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data['salaryGenerates']->withQueryString() }}
                </div>
            </div>
        </div>
    </div>
@endsection

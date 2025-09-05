<table>
    <tr>
        <th colspan="{{ 11 + count($data['additions']) + count($data['deductions']) }}" style="font-size: 24px; font-weight: bolder; text-align: center; background: #ebebeb;">
            {{ _trans('common.Salary Report') }}
        </th>
    </tr>
    <thead>
        <tr>
            <th rowspan="2">{{ _trans('common.Employee') }}</th>
            <th rowspan="2">{{ _trans('common.Month') }}</th>
            <th rowspan="2">{{ _trans('common.Salary') }}</th>
            <th colspan="{{ count($data['additions']) + 1 }}">{{ _trans('common.Addition') }}</th>
            <th colspan="{{ count($data['deductions']) + 3 }}">{{ _trans('common.Deduction') }}</th>
            <th rowspan="2">{{ _trans('common.Adjust') }}</th>
            <th rowspan="2">{{ _trans('common.Net Salary') }}</th>
            <th rowspan="2">{{ _trans('common.Paid') }}</th>
            <th rowspan="2">{{ _trans('common.Due') }}</th>
        </tr>
        <tr>
            @foreach ($data['additions'] ?? [] as $addition)
                <th>{{ $addition }}</th>
            @endforeach
            <th>{{ _trans('common.Total') }}</th>
            @foreach ($data['deductions'] ?? [] as $deduction)
                <th>{{ $deduction }}</th>
            @endforeach
            <th>{{ _trans('common.Advanced') }}</th>
            <th>{{ _trans('common.Absent') }}</th>
            <th>{{ _trans('common.Total') }}</th>
        </tr>
    </thead>
    <tbody>
        @php
            $thisPageTotalAddition = 0;
            $thisPageTotalDeduction = 0;
        @endphp
        @forelse ($data['salaryGenerates'] ?? [] as $salaryGenerate)
            <tr>
                <td>{{ $salaryGenerate->employee->name }} [{{ $salaryGenerate->employee->employee_id }}]</td>
                <td>{{ date('F Y', strtotime($salaryGenerate->date)) }}</td>
                <td>{{ currency_format(number_format($salaryGenerate->gross_salary, 2)) }}</td>

                @php
                    $additionAssoc = [];
                    foreach ($salaryGenerate->allowance_details as $item) {
                        $additionAssoc[$item['name']] = $item['amount'];
                    }
                    $totalAddition = 0;
                @endphp

                @foreach ($data['additions'] ?? [] as $addition)
                    <td>
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
                    <td>
                        @php
                            $amount = $deductionAssoc[$deduction] ?? 0;
                            $totaldeduction += $amount;
                            $thisPageTotalDeduction += $amount;
                        @endphp
                        {{ currency_format(number_format($amount, 2)) }}
                    </td>
                @endforeach
                
                <td>
                    {{ currency_format(number_format($salaryGenerate->advance_amount, 2)) }}
                </td>
                <td>
                    {{ currency_format(number_format($salaryGenerate->absent_amount, 2)) }}
                </td>
                <td>{{ currency_format(number_format($totaldeduction, 2)) }}</td>
                <td>{{ currency_format(number_format($salaryGenerate->adjust, 2)) }}</td>
                <td>{{ currency_format(number_format($salaryGenerate->net_salary, 2)) }}</td>
                <td>{{ currency_format(number_format($salaryGenerate->net_salary - $salaryGenerate->due_amount, 2)) }}</td>
                <td>{{ currency_format(number_format($salaryGenerate->due_amount, 2)) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="30"></td>
            </tr>
        @endforelse
        <tr>
            <th colspan="3" style="font-weight: 600">
                {{ _trans('common.Total') }}
            </th>
            @if (count($data['additions']))
                <th colspan="{{ count($data['additions']) }}"></th>
            @endif
            <th style="font-weight: 600">
                {{ currency_format(number_format($thisPageTotalAddition, 2)) }}
            </th>
            <th colspan="{{ count($data['deductions']) + 2 }}"></th>
            <th style="font-weight: 600">
                {{ currency_format(number_format($thisPageTotalDeduction, 2)) }}
            </th>
            <th style="font-weight: 600">
                {{ currency_format(number_format($data['salaryGenerates']->sum('adjust'), 2)) }}
            </th>
            <th style="font-weight: 600">
                {{ currency_format(number_format($data['salaryGenerates']->sum('net_salary'), 2)) }}
            </th>
            <th style="font-weight: 600">
                {{ currency_format(number_format($data['salaryGenerates']->sum('net_salary') - $data['salaryGenerates']->sum('due_amount'), 2)) }}
            </th>
            <th style="font-weight: 600">
                {{ currency_format(number_format($data['salaryGenerates']->sum('due_amount'), 2)) }}
            </th>
        </tr>
    </tbody>
</table>

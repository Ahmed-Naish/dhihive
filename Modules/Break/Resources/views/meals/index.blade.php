@extends('backend.layouts.app')

@section('title', @$data['title'])

@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        route('user.index') => _trans('common.Employees'),
        '#' => @$data['title'],
    ]) !!}


    <div class="table-content table-basic mb-3">
        <div class="card ot-card">
            <div class="card-body">
                <div class="row">

                    <form action="{{ $data['url'] }}" method="GET" enctype="multipart/form-data">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label" for="date">{{ _trans('breaks.Date') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control ot-form-control ot-input" id="date"
                                        name="date" value="{{ request('date', now()->toDateString()) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label"
                                        for="date">{{ _trans('breaks.Choose Type Based on Date') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control ot-form-control ot-input select2" name="type" required>
                                        <option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Both
                                        </option>
                                        <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ request('type') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label"
                                        for="date">{{ _trans('breaks.Choose A Department') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control ot-form-control ot-input select2" name="department_id" required>
                                        <option value="All" {{ request('department_id') == 'All' ? 'selected' : '' }}> {{ _trans('breaks.All Department') }}
                                        </option>
                                        @foreach( $data['departments']??[] as $department)
                                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group mt-3">
                                    <button type="submit"
                                        class="btn btn-gradian mr-3">{{ _trans('breaks.Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @if (@$data['data'])
        <!-- Total Counts as Cards -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-white">{{ _trans('breaks.Total Meals Today') }}</h5>
                        <p class="card-text text-white" id="today_meal_count">
                            {{ $data['today_meal'] }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title text-white">Total Meals Next Day</h5>
                        <p class="card-text text-white" id="next_day_meal_count">
                            {{ $data['next_day_meal_count'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meal Status Table -->
        <div class="table-content table-basic">
            <div class="card ot-card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th scope="col">{{ _trans('breaks.SL') }}</th>
                                <th scope="col">{{ _trans('breaks.Name') }}</th>
                                <th scope="col">{{ _trans('breaks.Meal') }} [ {{ $data['today'] }}]</th>
                                <th scope="col">{{ _trans('breaks.Next Meal') }} [ {{ $data['next_day'] }}]</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $next_date = \Carbon\Carbon::parse(request('date'))->addDay()->toDateString();
                            @endphp
                            @foreach ($data['data'] as $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + $data['data']->firstItem() - 1 }}</td>
                                    <td>{{ optional($row->employee)->name }} [{{ optional($row->employee)->employee_id }}]
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span
                                                class="badge badge-{{ $row->is_take_meal ? 'success' : 'danger' }} is_take_meal_{{ $row->employee->id }}">
                                                {{ $row->is_take_meal ? 'Yes' : 'No' }}
                                            </span>
                                            @if (hasPermission('meal_update'))
                                                <a href="javascript:void(0)" class="text-success fw-normal fs-3"
                                                    onclick="toggleStatus({{ $row->employee->id }}, '{{ $row->date }}', 'is_take_meal', {{ $row->is_take_meal ? 0 : 1 }})">
                                                    <i
                                                        class="las la-toggle-{{ $row->is_take_meal ? 'on' : 'off' }} text-{{ $row->is_take_meal ? 'success' : 'danger' }}"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span
                                                class="badge badge-{{ $row->next_day_meal ? 'success' : 'danger' }} next_day_meal{{ $row->employee->id }}">
                                                {{ $row->next_day_meal ? 'Yes' : 'No' }}
                                            </span>
                                            @if (hasPermission('meal_update'))
                                                <a href="javascript:void(0)" class="text-success fw-normal fs-3"
                                                    onclick="toggleStatus({{ $row->employee->id }}, '{{ $next_date }}', 'next_day_meal', {{ $row->next_day_meal ? 0 : 1 }})">
                                                    <i
                                                        class="las la-toggle-{{ $row->next_day_meal ? 'on' : 'off' }} text-{{ $row->next_day_meal ? 'success' : 'danger' }}"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>





                    </table>

                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted">
                                Showing {{ $data['data']->firstItem() }} to {{ $data['data']->lastItem() }} of
                                {{ $data['data']->total() }} entries
                            </p>
                        </div>
                        {{ $data['data']->links('backend.pagination.custom_link') }}
                    </div>

                </div>
            </div>
        </div>
    @endif
@endsection

@section('script')
    <script>
        function toggleStatus(userId, date, type, status) {
            $.ajax({
                url: '{{ route('meals.update-status') }}',
                type: 'POST',
                data: {
                    user_id: userId,
                    date: date,
                    type: type,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Status updated successfully.', 'Success');

                        const statusText = status ? 'Yes' : 'No';
                        const statusClass = status ? 'badge-success text-success' : 'badge-danger text-white';

                        if (type === 'is_take_meal') {
                            const takeMealElement = $(`.is_take_meal_${userId}`);
                            takeMealElement.text(statusText);
                            takeMealElement.removeClass('badge-danger badge-success text-white text-success')
                                .addClass(statusClass);

                            // #today_meal_count
                            const todayMealCountElement = document.getElementById('today_meal_count');
                            const todayMealCount = parseInt(todayMealCountElement.innerText);
                            todayMealCountElement.innerText = status ? todayMealCount + 1 : todayMealCount - 1;
                        } else if (type === 'next_day_meal') {
                            const nextDayMealElement = $(`.next_day_meal${userId}`);
                            nextDayMealElement.text(statusText);
                            nextDayMealElement.removeClass(
                                'badge-danger badge-success text-danger text-success').addClass(statusClass);


                            // #next_day_meal_count
                            const nextDayMealCountElement = document.getElementById('next_day_meal_count');
                            const nextDayMealCount = parseInt(nextDayMealCountElement.innerText);
                            nextDayMealCountElement.innerText = status ? nextDayMealCount + 1 :
                                nextDayMealCount - 1;
                        }

                        const iconElement = document.querySelector(
                            `a[onclick="toggleStatus(${userId}, '${date}', '${type}', ${status})"] i`);
                        if (status === 1) {
                            iconElement.classList.remove('la-toggle-off', 'text-danger');
                            iconElement.classList.add('la-toggle-on', 'text-success');
                        } else {
                            iconElement.classList.remove('la-toggle-on', 'text-success');
                            iconElement.classList.add('la-toggle-off', 'text-danger');
                        }



                    } else {
                        toastr.error('Failed to update status.', 'Error');
                        toastr.error(response.message, 'Error');

                    }
                },
                error: function(error) {
                    const errorMessage = error.responseJSON && error.responseJSON.message ? error.responseJSON
                        .message : 'Failed to update status.';
                    toastr.error(errorMessage, 'Error');
                }
            });
        }
    </script>
@endsection

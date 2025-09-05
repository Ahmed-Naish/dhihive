@extends('backend.layouts.app')
@section('title', @$data['title'])
@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        route('user.index') => _trans('common.Employees'),
        '#' => @$data['title'],
    ]) !!}
    <style>
        .btn {
            padding: 10px 15px;
            font-size: 14px;
        }
    </style>
    <div class="table-content table-basic">
        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ $data['url'] }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="title">{{ _trans('common.Title') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control ot-form-control ot-input" id="title"
                                    name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="limit">{{ _trans('common.Limit') }} <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control ot-form-control ot-input"
                                    id="limit" name="limit" value="{{ old('limit') }}" required>
                                @error('limit')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="limit_type">{{ _trans('common.Limit Type') }} <span
                                        class="text-danger">*</span></label>
                                <select name="limit_type" required
                                    class="form-select demo-select2-placeholder {{ $errors->has('limit_type') ? 'is-invalid' : '' }} select2">
                                    <option value="" selected>{{ _trans('common.Choose One') }}
                                    </option>
                                    <option value="day" {{ old('limit_type') == 'day' ? 'selected' : '' }}>
                                        {{ _trans('common.Day') }}</option>
                                    <option value="week" {{ old('limit_type') == 'week' ? 'selected' : '' }}>
                                        {{ _trans('common.Week') }}</option>
                                    <option value="month" {{ old('limit_type') == 'month' ? 'selected' : '' }}>
                                        {{ _trans('common.Month') }}</option>
                                    <option value="year" {{ old('limit_type') == 'year' ? 'selected' : '' }}>
                                        {{ _trans('common.Year') }}</option>
                                </select>
                                @error('limit_type')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label" for="max_duration">{{ _trans('common.Max Duration') }} <span
                                        class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control ot-form-control ot-input"
                                    id="max_duration" name="max_duration" value="{{ old('max_duration') }}" required>
                                @error('max_duration')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">{{ _trans('common.Duration Type') }} <span
                                        class="text-danger">*</span></label>
                                <select name="duration_type" required
                                    class="form-select demo-select2-placeholder {{ $errors->has('duration_type') ? 'is-invalid' : '' }} select2">
                                    <option value="" selected>{{ _trans('common.Choose One') }}
                                    </option>
                                    <option value="hour" {{ old('duration_type') == 'hour' ? 'selected' : '' }}>
                                        {{ _trans('common.Hour') }}</option>
                                    <option value="minute" {{ old('duration_type') == 'minute' ? 'selected' : '' }}>
                                        {{ _trans('common.Minute') }}</option>
                                </select>
                                @error('duration_type')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                           <div class="d-flex gap-3  mt-3 p-2">
                            <div class="form-check">
                                <label class="form-label mt-3 p-2" class="form-check-label" for="is_remark_required">
                                    {{ _trans('common.Is Remark Required') }}

                                    <input class="form-check-input" type="checkbox" id="is_remark_required"
                                        name="is_remark_required" value="1">
                                </label>
                            </div>
                            {{-- <div class="form-check  ">
                                <label class="form-label mt-3 p-2" class="form-check-label" for="will_ask_next_meal">
                                    Will ask for Next Meal

                                    <input class="form-check-input" type="checkbox" id="will_ask_next_meal"
                                        name="will_ask_next_meal" value="1">
                                </label>
                            </div> --}}
                           </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label" for="description">{{ _trans('common.Description') }} <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control text-area-height-unset" rows="5" id="description" name="description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div style="color: red;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label" for="description">{{ _trans('common.Icon') }}<span class="text-danger">*</span></label>
                                <div class="ot_fileUploader left-side">
                                    <input class="form-control" type="text" placeholder="{{ _trans('profile.Icon') }}" name="icon" readonly="" id="placeholder3">
                                    <div class="primary-btn-small-input">
                                        <label class="btn btn-lg ot-btn-primary" for="fileBrouse3">{{ _trans('common.Browse') }}</label>
                                        <input type="file" class="d-none form-control" name="icon" id="fileBrouse3">
                                    </div>
                                </div>
                                @if ($errors->has('icon'))
                                    <span class="text-danger">{{ $errors->first('icon') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gradian mr-3">{{ _trans('common.Submit') }}</button>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')

@endsection

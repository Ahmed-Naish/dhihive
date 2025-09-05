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
            <form action="{{ $data['url'] }}" enctype="multipart/form-data" method="post" id="attendanceForm">
                @csrf
                <div class="row gy-3">
                    <div class="col-lg-6">
                        <div class="row gy-3">
                            <div class="col-lg-12">
                                <h6>{{ _trans('common.Add New Plan') }}</h6>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->title : old('title') }}" required
                                        placeholder="{{ _trans('common.Enter Title') }}">
                                    @if ($errors->has('title'))
                                        <div class="error">{{ $errors->first('title') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Sub Title') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="subtitle" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->subtitle : old('subtitle') }}" required
                                        placeholder="{{ _trans('common.Enter Sub Title') }}">
                                    @if ($errors->has('subtitle'))
                                        <div class="error">{{ $errors->first('subtitle') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Price') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->price : old('price') }}" required
                                        placeholder="{{ _trans('common.2000') }}">
                                    @if ($errors->has('price'))
                                        <div class="error">{{ $errors->first('price') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('account.Duration Type') }} <span class="text-danger">*</span></label>
                                    <select name="duration_type" class="form-control select2" required>
                                        @foreach (['monthly', 'quarterly', 'yearly', 'lifetime'] as $option)
                                            <option value="{{ $option }}"
                                                {{ @$data['edit'] ? ($data['edit']->duration_type == $option ? 'selected' : '') : '' }}>
                                                {{ $option }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('duration_type'))
                                        <div class="error">{{ $errors->first('duration_type') }}</div>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('account.Popular') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="is_popular" class="form-control select2" required>
                                        <option value="1"
                                            {{ @$data['edit'] ? ($data['edit']->is_popular == 1 ? 'Selected' : '') : '' }}>
                                            {{ _trans('common.Yes') }}</option>
                                        <option value="0"
                                            {{ @$data['edit'] ? ($data['edit']->is_popular == 0 ? 'Selected' : '') : '' }}>
                                            {{ _trans('common.No') }}</option>
                                    </select>
                                    @if ($errors->has('is_popular'))
                                        <div class="error">{{ $errors->first('is_popular') }}</div>
                                    @endif
                                </div>
                            </div>
        
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('account.Status') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="status_id" class="form-control select2" required>
                                        <option value="1"
                                            {{ @$data['edit'] ? ($data['edit']->status_id == 1 ? 'Selected' : '') : '' }}>
                                            {{ _trans('payroll.Active') }}</option>
                                        <option value="4"
                                            {{ @$data['edit'] ? ($data['edit']->status_id == 4 ? 'Selected' : '') : '' }}>
                                            {{ _trans('payroll.Inactive') }}</option>
                                    </select>
                                    @if ($errors->has('status_id'))
                                        <div class="error">{{ $errors->first('status_id') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row gy-3">
                            <div class="col-lg-12">
                                <h6>{{ _trans('common.Add Limitation') }}</h6>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Project Management') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="limitations[]" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->limitations[0] : ''  }}"
                                        required placeholder="{{ _trans('common.Project Management') }}">
                                    @if ($errors->has('limitations.0'))
                                        <div class="error">{{ $errors->first('limitations.0') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Task Management') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="limitations[]" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->limitations[1] : old('limitations.1') }}"
                                        required placeholder="{{ _trans('common.Task Management') }}">
                                    @if ($errors->has('limitations.1'))
                                        <div class="error">{{ $errors->first('limitations.1') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Sales') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="limitations[]" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->limitations[2] : old('limitations.2') }}"
                                        required placeholder="{{ _trans('common.Sales') }}">
                                    @if ($errors->has('limitations.2'))
                                        <div class="error">{{ $errors->first('limitations.2') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-label">{{ _trans('common.Clients') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="limitations[]" class="form-control ot-form-control ot_input"
                                        value="{{ @$data['edit'] ? $data['edit']->limitations[3] : old('limitations.3') }}"
                                        required placeholder="{{ _trans('common.Clients') }}">
                                    @if ($errors->has('limitations.3'))
                                        <div class="error">{{ $errors->first('limitations.3') }}</div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                @if (@$data['url'])
                    <div class="row  mt-20">
                        <div class="col-md-12">
                            <div class="text-right d-flex justify-content-end">
                                <button class="crm_theme_btn ">{{ _trans('common.Save') }}</button>
                            </div>
                        </div>
                    </div>
                @endif


            </form>
        </div>
    </div>
</div>

<input type="hidden" id="get_user_url" value="{{ route('user.getUser') }}">
@endsection

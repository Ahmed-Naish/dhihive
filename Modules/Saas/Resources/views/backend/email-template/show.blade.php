@extends('backend.layouts.app')
@section('title', @$data['title'])

@section('style')
    <style>
        .form-control:disabled, .form-check-input:disabled {
            background: #e9ecef !important
        }
    </style>
@endsection

@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}

        <div class="row">
            <div class="col-lg-12">
                <div class="table-content table-basic mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ _trans('common.Detail') }}</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> 
                                            {{ _trans('common.Title') }} : {{ $data['template']->title }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> 
                                            {{ _trans('common.Subject') }} : {{ $data['template']->subject }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> 
                                            {{ _trans('common.Status') }} : 
                                            @if($data['template']->status_id == 1)
                                            {{ _trans('common.Active') }}
                                            @else
                                            {{ _trans('common.Inactive') }}
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> {{ _trans('common.Content') }} :</label>
                                        <div class="mt-3">
                                            {!! $data['template']->content !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('script')
    
@endsection

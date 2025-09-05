@extends('backend.layouts.app')
@section('title', @$title)

@section('style')
    <style>
        .form-control:disabled, .form-check-input:disabled {
            background: #e9ecef !important
        }
    </style>
@endsection

@section('content')
    {!! breadcrumb([
        'title' => @$title,
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$title,
    ]) !!}

    <form action="{{ $url }}" class="p-0" method="post" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="table-content table-basic mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ _trans('common.Email Template') }}</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> {{ _trans('common.Title') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="title" 
                                            class="form-control ot-form-control ot-input"
                                            placeholder="{{ _trans('common.Title') }}"
                                            value="{{ old('title') }}" 
                                            required>
                                        @if($errors->has('title'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('title') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> {{ _trans('common.Subject') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input 
                                            type="text" 
                                            name="subject" 
                                            class="form-control ot-form-control ot-input"
                                            placeholder="{{ _trans('common.Subject') }}"
                                            value="{{ old('subject') }}" 
                                            required>
                                        @if($errors->has('subject'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('subject') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> {{ _trans('common.Useful Short Codes') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            @foreach(emailTemplateShortCode() as $key => $item)
                                                <button type="button" id="shortCode_emp_first_name{{$key}}" value="[emp_first_name]" class="btn btn-outline-primary mb-3 copy-button" data-value="{{ $item['value'] }}">
                                                [{{ htmlspecialchars($item['value']) }}]
                                                </button> &nbsp;
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label"> {{ _trans('common.Content') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea type="text" name="content" placeholder="{{ _trans('common.Enter Content') }}"
                                            class="form-control content ot-form-control" required>{{ old('content') }}</textarea>
                                        @if($errors->has('content'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('content') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            {{ _trans('common.Status') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="status_id" class="form-control select2">
                                            <option value="1" {{ old('status_id') == 1 ? 'selected' : '' }}>{{ _trans('common.Active') }}</option>
                                            <option value="4" {{ old('status_id') == 4 ? 'selected' : '' }}>{{ _trans('common.Inactive') }}</option>
                                        </select>
                                        @if($errors->has('status_id'))
                                            <div class="invalid-feedback d-block">{{ $errors->first('status_id') }}</div>
                                        @endif
                                    </div>                                
                                </div>
                                <div class="form-group d-flex justify-content-end mt-3 mb-5">
                                    <button type="submit" class="btn btn-gradian fs-5" id="submitBtn">
                                        <i class="las la-check-circle me-1"></i> {{ @$button }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script src="{{ global_asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ global_asset('ckeditor/config.js') }}"></script>
    <script src="{{ global_asset('ckeditor/styles.js') }}"></script>
    <script src="{{ global_asset('ckeditor/build-config.js') }}"></script>
    <script src="{{ global_asset('backend/js/global_ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.copy-button').click(function() {
                var valueToCopy = $(this).data('value');
                var tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val('['+valueToCopy+']').select();
                document.execCommand('copy');
                tempInput.remove();
                // Show Toastr alert
                toastr.success('Copied to clipboard!', 'Success', {
                    closeButton: true,
                    progressBar: true,
                });
                $(button).addClass('btn-success');
            });
        });
    </script>
@endsection

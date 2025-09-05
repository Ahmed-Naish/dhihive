@extends('backend.layouts.app')
@section('title', @$title)
@section('content')
    {!! breadcrumb([
        'title' => @$title,
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$title,
    ]) !!}
    <div class="table-content table-basic">
        <div class="card">
            <div class="card-body">
                <form action="{{ $url }}" class="row p-0" method="post" enctype="multipart/form-data">
                    @csrf
                    {{-- dynamic attributes --}}
                    @if (@$attributes)
                        @foreach (@$attributes as $key => $attribute)
                            <div class="{{ @$attribute['col'] }}">
                                <label class="col-form-label">
                                    @if (@$attribute['required'])
                                        <span class="text-danger">*</span>
                                    @endif
                                    {{ @$attribute['label'] }}

                                    @if (Str::lower(@$attribute['label']) == 'order')
                                        ({{ _trans('common.Use 0 for Hero Section') }})
                                    @endif
                                </label>
                                @if (@$attribute['type'] == 'text')
                                    <input type="text" class="{{ @$attribute['class'] }}" name="{{ @$key }}"
                                        id="{{ @$key }}" placeholder="{{ @$attribute['label'] }}"
                                        @if (@$attribute['required']) required @endif autocomplete="off"
                                        value="{{ @$attribute['value'] ?? old(@$key) }}">
                                @elseif (@$attribute['type'] == 'select')
                                    <select name="{{ @$key }}" id="{{ @$attribute['id'] }}"
                                        class="{{ @$attribute['class'] }}" aria-label="Default select example"
                                        @if (@$attribute['required']) required @endif {{ @$attribute['multiple'] }}>
                                        @foreach (@$attribute['options'] as $option)
                                            <option value="{{ $option['value'] }}"
                                                {{ @$option['active'] ? 'selected' : '' }}>
                                                <?= $option['text'] ?>
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif (@$attribute['type'] == 'number')
                                    <input type="number" class="{{ @$attribute['class'] }}" name="{{ @$key }}"
                                        id="{{ @$key }}" @if (@$attribute['required']) required @endif
                                        value="{{ @$attribute['value'] ?? old(@$key) }}" autocomplete="off">
                                @elseif (@$attribute['type'] == 'date')
                                    <input type="text" class="{{ @$attribute['class'] }}" name="{{ @$key }}"
                                        id="{{ @$attribute['id'] }}" @if (@$attribute['required']) required @endif
                                        value="{{ @$attribute['value'] ?? old(@$key) }}" autocomplete="off">
                                @elseif (@$attribute['type'] == 'file')
                                    <div class="ot_fileUploader left-side mb-3">
                                        <input class="form-control" type="text"
                                            placeholder="{{ @$attribute['placeholder'] }}" name="" readonly=""
                                            id="placeholder">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary"
                                                for="fileBrouse">{{ _trans('common.Browse') }}</label>
                                            <input type="file" class="d-none form-control" name="{{ @$key }}"
                                                id="fileBrouse">
                                        </button>
                                    </div>
                                @elseif (@$attribute['type'] == 'checkbox')
                                    <div class="form-check">
                                        <input type="checkbox" class="{{ @$attribute['class'] }}"
                                            name="{{ @$key }}" id="{{ @$key }}" value="1">
                                        <label class="form-check-label">{{ @$attribute['label'] }}</label>
                                    </div>
                                @elseif (@$attribute['type'] == 'textarea')
                                    <textarea class="{{ @$attribute['class'] }}" name="{{ @$key }}" rows="{{ @$attribute['row'] ?? 1 }}"
                                        placeholder="{{ @$attribute['label'] }}" @if (@$attribute['required']) required @endif>{{ @$attribute['value'] ?? old($key) }}</textarea>
                                @elseif (@$attribute['type'] == 'color')
                                    <input type="color" class="{{ @$attribute['class'] }}" name="{{ @$key }}"
                                        id="{{ @$key }}" placeholder="{{ @$attribute['label'] }}"
                                        @if (@$attribute['required']) required @endif autocomplete="off"
                                        value="{{ @$attribute['value'] ?? old(@$key) }}">
                                @elseif (@$attribute['type'] == 'radio')
                                    <fieldset>

                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach (@$attribute['options'] as $option)
                                            <div class="cms_style">
                                                    <input type="radio" id="{{ $option['label'] }}" name="style"
                                                    value="{{ $option['value'] }}" {{ $option['checked'] }}/>
                                                    <label for="{{ $option['label'] }}">
                                                        <span class="cms_style_image">
                                                            @if(@$option['image'])
                                                                <img src="{{ $option['image'] }}" alt="" class="w-50">
                                                            @endif
                                                        </span>
                                                        <span class="cms_style_name d-block">{{ $option['label'] }}</span>
                                                    </label>
                                            </div>
                                            @endforeach
                                        </div>


                                    </fieldset>
                                @endif

                                @error($key)
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach

                    @endif
                    <div class="form-group d-flex justify-content-end">
                        <button type="submit" class="btn btn-gradian mr-3">{{ @$button }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <input type="hidden" id="get_user_url" value="{{ route('user.getUser') }}">
@endsection
@section('script')
@endsection

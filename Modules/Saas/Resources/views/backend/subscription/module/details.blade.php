@extends('backend.layouts.app')
@section('title', @$data['title'])
@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}

    <div class=" table-content table-basic ">
        <div class="card">
            <div class="card-body">
                <div class="text-end">
                    <a href="javascript:void(0)" role="button" class="crm_theme_btn " id="addButton">
                        <span><i class="fa-solid fa-plus"></i></span>
                        <span class="d-xl-inline">{{ _trans('common.Add') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <form id="moduleForm" action="{{ route('saas.subscription_modules.assignStore', $data['module']->id) }}"
            method="POST">
            @csrf
            <input type="hidden" name="module_id" value="{{ $data['module']->id }}">

            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <span
                            class=" text-muted fs-6">{{ _trans('subscription.[You can add multiple item by clicking add button]') }}</span>

                    </div>
                    <div class="card-form"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Module Terms') }}<span
                                        class="text-danger">*</span></label>
                                <select name="module_term_id[]" class="form-select select2 w-100"
                                    id="_module_term_id_${index}">
                                    @foreach ($data['module-terms'] as $moduleTerm)
                                        <option value="{{ @$moduleTerm->id }}">{{ @$moduleTerm->title }}</option>
                                    @endforeach
                                </select>
                                <div id="moduleTermError_${index}" class="invalid-feedback d-none"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ _trans('common.Module Values') }}<span
                                        class="text-danger">*</span></label>
                                <select name="module_value_id[]" class="form-select select2 w-100"
                                    id="_module_value_id_${index}">
                                    @foreach ($data['module-values'] as $moduleValue)
                                        <option value="{{ @$moduleValue->id }}">{{ @$moduleValue->title }}</option>
                                    @endforeach
                                </select>
                                <div id="moduleValueError_${index}" class="invalid-feedback d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="text-end mt-3">
                <button type="submit" class="crm_theme_btn ">{{ _trans('common.Submit') }}</button>
            </div>
        </form>

    </div>
@endsection
@section('script')


    <script>
        // Get references to the elements
        const addButton = document.getElementById("addButton");

        // Initialize the counter variable
        let index = 0;

        // Add event listener to the "Add" button
        addButton.addEventListener("click", function() {

            // Create a new select container
            const newContainer = document.createElement("div");
            newContainer.className = "card-form";

            newContainer.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label">{{ _trans('common.Module Terms') }}<span class="text-danger">*</span></label>
                    <select name="module_term_id[]" class="form-select select2 w-100" id="_module_term_id_${index}">
                        @foreach ($data['module-terms'] as $moduleTerm)
                            <option value="{{ @$moduleTerm->id }}">{{ @$moduleTerm->title }}</option>
                        @endforeach
                    </select>
                    <div id="moduleTermError_${index}" class="invalid-feedback d-none"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label">{{ _trans('common.Module Values') }}<span class="text-danger">*</span></label>
                    <select name="module_value_id[]" class="form-select select2 w-100" id="_module_value_id_${index}">
                        @foreach ($data['module-values'] as $moduleValue)
                            <option value="{{ @$moduleValue->id }}">{{ @$moduleValue->title }}</option>
                        @endforeach
                    </select>
                    <div id="moduleValueError_${index}" class="invalid-feedback d-none"></div>
                </div>
            </div>
        </div>
    `;

            // Increment the counter variable
            index++;

            // Append the new container to the table content
            const tableContent = document.querySelector(".card-form");
            tableContent.appendChild(newContainer);

            // Initialize Select2 for the newly created select elements
            const selectElements = newContainer.querySelectorAll(".select2");
            selectElements.forEach(function(selectElement) {
                $(selectElement).select2();
            });
        });
    </script>


@endsection

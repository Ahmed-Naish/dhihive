@extends('backend.layouts.app')
@section('title', @$data['title'])
@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        route('user.index') => _trans('common.Employees'),
        '#' => @$data['title'],
    ]) !!}
    <div class="table-content table-basic">
        <div class="card">
            <div class="card-body">
                <div
                    class="table-toolbar d-flex flex-wrap gap-2 flex-xl-row justify-content-center justify-content-xxl-between align-content-center pb-3">
                    <div class="align-self-center">
                        <div class="d-flex flex-wrap gap-2  flex-lg-row justify-content-center align-content-center">
                            <!-- show per page -->
                            <div class="align-self-center">
                                <label>
                                    <span class="mr-8">{{ _trans('common.Show') }}</span>
                                    <select class="form-select d-inline-block" id="entries" onchange="updateRecords()">
                                        @include('backend.partials.tableLimit') 
                                    </select>
                                    <input type="hidden" name="page" id="page" value="1">
                                    <span class="ml-8">{{ _trans('common.Entries') }}</span>
                                </label>
                            </div>

                            <div class="align-self-center d-flex flex-wrap gap-2">
                                <!-- add btn -->
                                <div class="align-self-center">
                                    <div class="search-box d-flex">
                                        <input class="form-control" placeholder="{{ _trans('common.Search') }}"
                                            name="search" onkeyup="updateRecords()" autocomplete="off" id="search"
                                            value="{{ @$data['input']['search'] }}">
                                        <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="align-self-center">
                                <button type="button" class="btn-daterange" id="daterange" data-bs-toggle="tooltip"
                                    data-bs-placement="right" data-bs-title="{{ _trans('common.Date Range') }}">
                                    <span class="icon"><i class="fa-solid fa-calendar-days"></i>
                                    </span>
                                    <span class="d-none d-xl-inline">{{ _trans('common.Date Range') }}</span>
                                </button>
                                <input type="hidden" id="daterange-input" onchange="updateRecords()">
                            </div>
                        </div>
                    </div>

                    <div class="align-self-center">
                        <!-- add btn -->
						@if (hasPermission('break_create'))
							<div class="align-self-center">
								<a href="{{ route('break.create') }}" role="button" class="btn-add"
								   data-bs-toggle="tooltip" data-bs-placement="right"
								   data-bs-title="{{ _trans('common.Add') }}">
									<span><i class="fa-solid fa-plus"></i> </span>
									<span class="d-none d-xl-inline">{{ _trans('common.Create') }}</span>
								</a>
							</div>
						@endif
                    </div>
                </div>


                <div class="table-responsive _ajaxData min-height-500">


                </div>
            </div>
        </div>
    </div>


    <input type="hidden" value="{{ @$data['url'] }}" id="break_url" name="break_url">
@endsection
@section('script')
<script src="{{ global_asset('modules/Break/assets/js/app.js') }}"></script>
@endsection

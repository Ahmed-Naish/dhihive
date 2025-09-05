@extends('backend.layouts.app')
@section('title', @$data['title'])

@section('style')
    <style>
        .table-content.table-basic .table .thead tr th {
            padding: 16px 10px !important;
        }

        .table-content.table-basic .table .tbody tr td {
            padding: 5px 10px !important;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            z-index: 1000;
        }

        #loader {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        #loader .d-flex {
            justify-content: center;
            align-items: center;
            height: 100%;
        }
    </style>
@endsection
@section('content')
    {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$data['title'],
    ]) !!}

    <x-saas::processing-subscriptions />

    <div class="table-content table-basic">
        <div class="card">
            <div class="card-body">
                <!-- toolbar table start -->
                <div
                    class="table-toolbar d-flex flex-wrap gap-2 flex-xl-row justify-content-center justify-content-xxl-between align-content-center pb-3">
                    <div class="align-self-center">
                        <div class="d-flex flex-wrap gap-2  flex-lg-row justify-content-center align-content-center">
                            <!-- show per page -->
                            <div class="align-self-center">
                                <label>
                                    <span class="mr-8">{{ _trans('common.Show') }}</span>
                                    <select class="form-select d-inline-block" id="entries" onchange="subscriptionDatatable()">
                                        <option selected value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="ml-8">{{ _trans('common.Entries') }}</span>
                                </label>
                            </div>
                            {{-- <div class="align-self-center">
                                <div class="search-box d-flex">
                                    <input class="form-control" placeholder="{{ _trans('common.Search') }}" name="search"
                                        onkeyup="subscriptionDatatable()" autocomplete="off" />
                                    <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                </div>
                            </div> --}}
                            <div class="d-flex d-lg-none">
                                @include('backend.partials.buttons')
                            </div>
                            {{-- exportOnMobileDevice::end --}}
                        </div>
                    </div>
                    <!-- export -->
                    <div class="d-none d-lg-flex">
                        @include('backend.partials.buttons')
                    </div>
                </div>
                <!-- toolbar table end -->
                <!--  table start -->
                <div class="table-responsive  min-height-500">

                    @include('backend.partials.table')

                </div>
                <!--  table end -->
            </div>
        </div>
    </div>


    <div class="overlay" id="overlay"></div>

    <div id="loader">
        <div class="d-flex justify-content-center align-items-center">
            <div class="spinner-border text-white" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="text-center mt-3">
            <p class="text-white">Please wait...</p>
        </div>
    </div>
@endsection
@section('script')
    @include('backend.partials.table_js')

    <script>
        const approveSubscription = (url) => {

            Swal.fire({
                title: $('#are_you_sure').val(),
                text: $('#you_want_approve').val(),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Approve",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {

                    $('#overlay').show();
                    $('#loader').show();
                    
                    $.ajax({
                        url,
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function ({status, message}) {
                            $('#overlay').hide();
                            $('#loader').hide();

                            if (status) {

                                toastr.success(message, "Success", {
                                    timeOut: 3000,
                                });

                                setTimeout(() => {
                                    location.reload(true);
                                }, 3000);
                            } else {
                                toastr.error("Something went wrong!", "Error!", {
                                    timeOut: 3000,
                                })
                            }
                        },
                        error: function (error) {
                            $('#overlay').hide();
                            $('#loader').hide();

                            toastr.error("Something went wrong!", "Error!", {
                                timeOut: 3000,
                            })
                        },
                    });
                } 
                // else if (result.dismiss === Swal.DismissReason.cancel) {}
            });
        }


        const rejectSubscription = (url) => {

            Swal.fire({
                title: $('#are_you_sure').val(),
                text: $('#you_want_reject').val(),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Reject",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {

                    $('#overlay').show();
                    $('#loader').show();
                    
                    $.ajax({
                        url,
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function ({status, message}) {
                            $('#overlay').hide();
                            $('#loader').hide();

                            if (status) {

                                toastr.success(message, "Success", {
                                    timeOut: 3000,
                                });

                                setTimeout(() => {
                                    location.reload(true);
                                }, 3000);
                            } else {
                                toastr.error("Something went wrong!", "Error!", {
                                    timeOut: 3000,
                                })
                            }
                        },
                        error: function (error) {
                            $('#overlay').hide();
                            $('#loader').hide();

                            toastr.error("Something went wrong!", "Error!", {
                                timeOut: 3000,
                            })
                        },
                    });
                }
            });
        }
    </script>
@endsection

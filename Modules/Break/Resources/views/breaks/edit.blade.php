@extends('backend.layouts.app')
@section('title', @$data['title'])
@section('content')
    {{-- {!! breadcrumb([
        'title' => @$data['title'],
        route('admin.dashboard') => _trans('common.Dashboard'),
        route('user.index') => _trans('common.Employees'),
        '#' => @$data['title'],
    ]) !!} --}}


    <style>
        .time-calculate {
            font-size: 13px;
            font-weight: 600;
            color: #878686;
            margin-bottom: 30px;
        }

        .info-break {
            font-size: 12px;
            font-weight: 600;
            color: var(--bs-breadcrumb-item-active-color);
        }

        .max-w-600 {
            max-width: 600px
        }

        .white-bg {
            background-color: #fff;
        }

        .p-check {
            padding: 50px 30px
        }

        .radious-4 {
            border-radius: 4px
        }

        .progress.break_out {
            background: #CD4040 !important;
            animation: zoomInOut 1s infinite alternate;
            transform-origin: center;
            border: 5px solid #DDD;
        }

        @keyframes zoomInOut {
            0% {
                transform: scale(1);
                /* Initial scale */
            }

            50% {
                transform: scale(1.1);
                /* Zoom in */
            }

            100% {
                transform: scale(1);
                /* Zoom out */
            }
        }
    </style>

    <div class="row justify-content-evenly">
        <div class="col-lg-5">
            <div class="row justify-content-center bg-white">
                <div class="p-check radious-4">

                    <h4 class="info-break text-center mb-20">{{ _trans('common.Your') }} <span
                            class="badge badge-warning">{{ @$data['break']->breakType->name }}</span> {{ _trans('common.is running') }}</h4>
                    <div class="d-flex justify-content-between   align-items-center mb-20">
                        <div class="left-div">
                            <h3 class="  mb-0 mt-0">{{ _trans('common.STARTED') }}</h3>
                            <p class="time-calculate  mb-0 mt-0">
                                {{ date('h:i:s A', strtotime($data['break']->start_time)) }}</p>
                        </div>
                        <div class="right-div">
                            <h3 class=" text-end mb-0 mt-0">{{ _trans('common.END') }}</h3>
                            <p id="endTime" class="time-calculate text-end mb-0 mt-0"></p>
                        </div>
                    </div>

                    <textarea id="remark" name="remark" cols="10" rows="5" class="w-100 mb-20"
                        @if ($data['break']->breakType->is_remark_required) required @endif
                        placeholder="Write a remarks @if ($data['break']->breakType->is_remark_required) (required) @endif "></textarea>
                    <input type="hidden" name="break_type_id" id="break_type_id"
                        value="{{ @$data['break']->break_type_id }}">
                    <input type="hidden" name="user_break_id" id="user_break_id" value="{{ @$data['break']->id }}">

                    {{-- if will_ask_next_meal =1 then show this input field --}}
                    {{-- @if ($data['break']->breakType->will_ask_next_meal == 1)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <div class="form-check will_ask_for_next_meal_box ">

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="flexSwitchCheckChecked" checked>
                                            <label class="form-check-label fs-15" for="flexSwitchCheckChecked">Do you need
                                                next
                                                day
                                                meal?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif --}}

                    <div class="form-group button-hold-container">
                        <button class="button-hold" id="button-hold">
                            <div>
                                <svg class="progress break_out" viewBox="0 0 32 32">
                                    <circle r="8" cx="16" cy="16" />
                                </svg>
                                <svg class="tick" viewBox="0 0 32 32">
                                    <polyline points="18,7 11,16 6,12" />
                                </svg>
                            </div>
                        </button>
                    </div>
                    <input type="hidden" id="form_url" value="{{ @$data['url'] }}">



                </div>
            </div>
        </div>
        <div class="col-lg-5 in-out-record">
            <div class="p-check radious-4 bg-white">
                <div class="record-list d-flex justify-content-center flex-column align-items-center">
                    <h5 class="mb-40">Today's Record</h5>

                    <ul class="m-0 p-0 w-100">
                        @foreach ($data['today_breaks'] as $break)
                            <li class="d-flex justify-content-around align-items-center pb-10 mb-10 border-bottom">
                                <p class="mb-0"> {{ $break->duration ?? '00:00:00' }}</p>
                                <span class="text-primary"> | </span>
                                <p>
                                    <strong class="text-capitalize">{{ $break->breakType->name }}</strong>
                                    <span class="d-block">{{ date('h:i:s A', strtotime($break->start_time)) }} to
                                        {{ date('h:i:s A', strtotime($break->end_time)) }}</span>
                                </p>
                            </li>
                        @endforeach
                    </ul>
                    {{-- Total Recored Time --}}
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center mt-40">
                        {{-- <span class="text-primary text-capitalize m-0">Total break time</span> --}}
                        <span class="text-primary text-capitalize m-0 text-danger">Total break time <span
                                class=" badge badge-danger">{{ @$data['totalDuration'] ?? '0 Min' }}</span> </span>
                    </div>
                    {{-- / --}}
                </div>
            </div>

            {{-- / --}}

        </div>
    </div>






@endsection
@section('script')

    {{-- onchnage document_type select  --}}
    <script>
        // updateContent

        $(document).ready(function() {
            $('#document_type').on('change', function() {
                updateContent();
            });
        });
    </script>

    <script src="{{ global_asset('backend/js/pages/__project.js') }}"></script>
    <script src="{{ global_asset('frontend/assets/js/iziToast.js') }}"></script>
    <script src="{{ global_asset('backend/js/image_preview.js') }}"></script>
    <script src="{{ global_asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ global_asset('ckeditor/config.js') }}"></script>
    <script src="{{ global_asset('ckeditor/styles.js') }}"></script>
    <script src="{{ global_asset('ckeditor/build-config.js') }}"></script>
    <script src="{{ global_asset('backend/js/global_ckeditor.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ensure CKEditor script is loaded
            if (typeof CKEDITOR !== 'undefined') {
                console.log("CKEditor script loaded successfully.");

                // Replace textarea with CKEditor and set the height
                CKEDITOR.replace('editor1Textarea', {
                    height: 500, // Set height to 500 pixels
                    on: {
                        instanceReady: function(evt) {
                            console.log("CKEditor instance is ready with height:", evt.editor.config
                                .height);
                        }
                    }
                });
            } else {
                console.error("CKEditor script is not loaded.");
            }
        });
    </script>



    <script>
        let checkInBtn = null;

        function btnHold() {
            let duration = 1600,
                success = button => {
                    //Success function
                    $('.progress').hide();
                    button.classList.add('success');
                    breakIn($('#form_url').val());
                };

            document.querySelectorAll('.button-hold').forEach(button => {
                checkInBtn = button;
                button.style.setProperty('--duration', duration + 'ms');
                ['mousedown', 'touchstart', 'keypress'].forEach(e => {
                    button.addEventListener(e, ev => {
                        if (e != 'keypress' || (e == 'keypress' && ev.which == 32 && !button
                                .classList.contains('process'))) {
                            button.classList.add('process');
                            button.timeout = setTimeout(success, duration, button);
                        }
                    });
                });
                ['mouseup', 'mouseout', 'touchend', 'keyup'].forEach(e => {
                    button.addEventListener(e, ev => {
                        if (e != 'keyup' || (e == 'keyup' && ev.which == 32)) {
                            button.classList.remove('process');
                            clearTimeout(button.timeout);
                        }
                    }, false);
                });
            });

        }
        btnHold();



        var breakUrl;
        var breakIn = (url) => {
            let will_ask_next_meal = 0;
            let break_type_id = $('#break_type_id').val();
            let user_break_id = $('#user_break_id').val();

            // Check if the textarea is empty and it's required
            if ($('#remark').attr('required') && $('#remark').val().trim() === '') {

                checkInBtn.classList.add('process');
                $('.process svg').css('display', 'block');
                checkInBtn.classList.remove('success');
                clearTimeout(checkInBtn.timeout);
                Toast.fire({
                    icon: 'error',
                    title: 'Remark is required!',
                });
                return false;
            }


            // if id #will_ask_next_meal exist, then check
            if ($('#will_ask_next_meal').length) {
                will_ask_next_meal = $('#will_ask_next_meal').is(':checked') ? 1 : 0;
            }
            let remark = $('#remark').val();
            // if id #will_ask_next_meal exist, then check
            if ($('#will_ask_next_meal').length) {
                will_ask_next_meal = $('#will_ask_next_meal').is(':checked') ? 1 : 0;
            }

            $.ajax({
                url: "{{ route('break.update') }}",
                type: 'POST',
                data: {
                    break_type_id: break_type_id,
                    user_break_id: user_break_id,
                    will_ask_next_meal: will_ask_next_meal,

                    type: 'break_back',
                    remark: remark,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success') {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 1500,
                        });
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response?.data?.message ?? 'Something went wrong!',
                        });
                    }
                }
            });
        }
    </script>
    <script>
        // Function to update end time every second
        function updateTime() {
            var currentTime = new Date();
            var formattedTime = currentTime.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('endTime').textContent = formattedTime;
        }

        // Update time initially
        updateTime();

        // Update time every second
        setInterval(updateTime, 1000);
    </script>
@endsection

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
        .single-checkin-bttons .form-check {
            padding: 10px 20px;
            background-color: #f5f5f5;
            display: flex;
            gap: 11px;
            align-items: center;
            border-radius: 4px;
            cursor: pointer;
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

        .progress.break_in {
            animation: zoomInOut 1s infinite alternate;
            transform-origin: center;
            border: 5px solid #ddd;
        }

        @keyframes zoomInOut {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
    <div class="container">

        <div class="row justify-content-center">
            <div class="max-w-600 white-bg p-check radious-4">

                {{-- Buttons --}}
                <div class="form-group d-flex flex-wrap gap-3 mb-40">
                    <div class="row">
                        <h4 class="text-center mb-3 text-uppercase mb-30">
                            {{ _trans('common.Choose your option') }}
                        </h4>
                        @foreach ($data['break_types'] ?? [] as $break_type)
                            <div class="col-md-6">
                                <div class="single-checkin-bttons mb-10">
                                    <label class="form-check" for="{{ $break_type->id }}">
                                        <input type="radio" class="form-check" name="break_type_id" id="{{ $break_type->id }}" value="{{ $break_type->id }}">
                                        {{ $break_type->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- Buttons --}}


                <div class="form-group">
                    <div class="timer-field pt-2 pb-2">
                        <h1 class="text-center">
                            <div class="clock company_name_clock fs-16 clock" id="clock" onload="currentTime()"> {{ _trans('attendance.00:00:00') }}</div>
                        </h1>
                    </div>
                </div>


                @if (@$data['reason'][0] == 'L')
                    <div class="form-group w-50 mx-auto mb-3">
                        <label class="form-label float-left">{{ _trans('common.Note') }} </label>
                        <textarea type="text" name="reason" id="reason" rows="3" class="form-control mt-0 ot-input"
                            onkeyup="textAreaValidate(this.value, 'error_show_reason')" placeholder="{{ _trans('common.Note') }}">{{ old('reason') }}</textarea>
                        <small class="error_show_reason text-left text-danger">
                        </small>
                    </div>
                @endif

                <div class="form-group button-hold-container">
                    <button class="button-hold" id="button-hold">
                        <div>
                            <svg class="progress break_in" viewBox="0 0 32 32">
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


        <
        script src = "{{ global_asset('backend/js/pages/__project.js') }}" >
    </script>
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
        function btnHold() {
            let duration = 1600,
                success = button => {
                    //Success function
                    $('.progress').hide();
                    button.classList.add('success');
                    breakIn($('#form_url').val());
                };
            document.querySelectorAll('.button-hold').forEach(button => {
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
            breakUrl = url;
            let break_type_id = $('input[name="break_type_id"]:checked').val();
            let reason = $('#reason').val();
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    break_type_id: break_type_id,
                    reason: reason,
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
@endsection

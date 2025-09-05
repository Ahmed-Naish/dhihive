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

        .qr-image-mx-w-300 img {
            max-width: 300px;
            margin: 0 auto;
        }
    </style>
    <div class="container">

        <div class="row justify-content-center">
            <div class="max-w-600 white-bg p-check radious-4">

                {{-- Buttons --}}
                <div class="form-group d-flex flex-wrap gap-3 mb-40 flex-column">
                    <h2 class="text-center mb-3 text-uppercase mb-30">
                        {{ _trans('breaks.Regenerate QR Code') }}
                    </h2>
                    @if (@$data['break_settings']->path != '')
                        <div class="qr-image-mx-w-300 text-center">
                            <img id="svg_path" src="{{ url($data['break_settings']->path) }}" alt="">
                        </div>
                        <p class="text-center text-info" id="qr_message"></p>
                    @else
                        <p class="text-center text-danger" id="qr_message">{{ _trans('breaks.No QR code found') }}</p>
                    @endif
                    {{-- Buttons --}}
                    <div class="text-center">
                        <button class="btn btn-gradian mr-3 text-center" onclick="reGenerateQrCode()">{{ _trans('breaks.Re Generate') }}</button>
                        <button class="btn btn-gradian mr-3 text-center" onclick="openPrintView()"> 
                            <i class="las la-print"></i> 
                            {{ _trans('breaks.Print') }}
                        </button>
                    </div>
                </div>
                {{-- Buttons --}}

                {{-- Form --}}
                <input type="hidden" id="form_url" value="{{ @$data['url'] }}">
            </div>
        </div>
    </div>




@endsection
@section('script')


    <script src="{{ global_asset('backend/js/pages/__project.js') }}"></script>
    <script src="{{ global_asset('frontend/assets/js/iziToast.js') }}"></script>
    <script src="{{ global_asset('backend/js/image_preview.js') }}"></script>
    <script src="{{ global_asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ global_asset('ckeditor/config.js') }}"></script>
    <script src="{{ global_asset('ckeditor/styles.js') }}"></script>
    <script src="{{ global_asset('ckeditor/build-config.js') }}"></script>
    <script src="{{ global_asset('backend/js/global_ckeditor.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>



    <script>
        $(document).ready(function() {
            reGenerateQrCode = () => {
                var generateQrURL = "{{ @$data['url'] }}";
                console.log("generateQrURL " +generateQrURL);
                
                // alert('Are you sure to re-generate QR Code?');
                Swal.fire({
                    title: 'Are you sure to re-generate QR Code?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Re-generate it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: generateQrURL,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    var svgElement = document.getElementById('svg_path');
                                    svgElement.src = response.path;
                                    $('#qr_message').text(response.text);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response?.data?.message ??
                                            'Something went wrong!',
                                    });
                                }
                            },
                            error: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Unable to generate QR code.',
                                });
                            }
                        });
                    }
                });
            }



            openPrintView = () => {
                var svgElement = document.getElementById('svg_path');

                // Create a container for the SVG
                var container = document.createElement('div');
                container.appendChild(svgElement.cloneNode(true));

                // Get the dimensions of the SVG
                var width = svgElement.naturalWidth || svgElement.width.baseVal.value;
                var height = svgElement.naturalHeight || svgElement.height.baseVal.value;

                // Convert dimensions to inches (assuming 96 DPI for screen resolution)
                var widthInInches = width / 96;
                var heightInInches = height / 96;

                var opt = {
                    margin: 0,
                    filename: 'QRCode.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2
                    },
                    jsPDF: {
                        unit: 'in',
                        format: [widthInInches, heightInInches],
                        orientation: 'portrait'
                    }
                };

                // Use html2pdf to create the PDF
                html2pdf().from(container).set(opt).toPdf().get('pdf').then(function(pdf) {
                    var pdfBlob = pdf.output('blob');
                    var pdfUrl = URL.createObjectURL(pdfBlob);
                    window.open(pdfUrl);
                });
            }

        });
    </script>

@endsection

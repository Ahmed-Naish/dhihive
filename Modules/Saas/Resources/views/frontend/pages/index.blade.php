@extends('saas::frontend.layouts.master')

@section('title', @$data['title'])

@section('styles')
    <style>
        .custom-title {
            font-style: normal;
            font-weight: 700;
            font-size: 36px;
            line-height: 49px;
            text-align: center;
            text-transform: capitalize;
            margin-bottom: 35px;
        }
        .custom-pera {    
            font-style: normal;
            font-weight: 400;
            font-size: 16px;
            line-height: 22px;
            margin-bottom: 36px;
        }
        .custom-image{
            margin-bottom: 15px;
        }
    </style>
@endsection 

@section('content')
    @foreach(@$data['cms_contents'] as $key => $content)
        <section class="{{ $content->style != 6 || $content->style != 7 ? 'section-padding' : 'started-page-screen' }}" style="background-color: {{ $content->bg_color }}">
            @if($content->style == 1)
                <div class="container">
                    <div class="row gap-12x">
                        <div class="col-lg-12"><h3 class="custom-title" style="color: {{@$content->text_color}} !important;"> {{ @$content->title }}</h3></div>
                        <div class="col-lg-12"><div class="custom-pera custom-pera{{ $content->id }}" style="color: {{@$content->text_color}} !important;">{!! $content->description !!}</div></div>
                        <div class="col-lg-12"> <img class="custom-image" src="{{ uploaded_asset(@$content->attachment_id) }}" class="w-100" alt=""></div>
                    </div>
                </div>
            @elseif($content->style == 2)
                <div class="container">
                    <div class="row gap-12x">
                        <div class="col-lg-12"><h3 class="custom-title" style="color: {{@$content->text_color}} !important;"> {{ @$content->title }}</h3></div>
                        <div class="col-lg-4"><div class="custom-pera custom-pera{{ $content->id }}" style="color: {{@$content->text_color}} !important;">{!! $content->description !!}</div></div>
                        <div class="col-lg-8"> <img class="custom-image" src="{{ uploaded_asset(@$content->attachment_id) }}" class="w-100" alt=""></div>
                    </div>
                </div> 
            @elseif($content->style == 3)
                <div class="container">
                    <div class="row gap-12x">
                        <div class="col-lg-12"><h3 class="custom-title" style="color: {{@$content->text_color}} !important;"> {{ @$content->title }}</h3></div>
                        <div class="col-lg-6"><div class="custom-pera custom-pera{{ $content->id }}" style="color: {{@$content->text_color}} !important;">{!! $content->description !!}</div></div>
                        <div class="col-lg-6"> <img class="custom-image" src="{{ uploaded_asset(@$content->attachment_id) }}" class="w-100" alt=""></div>
                    </div>
                </div> 
            @elseif($content->style == 4)
                <div class="container">
                    <div class="row gap-12x">
                        <div class="col-lg-12"><h3 class="custom-title" style="color: {{@$content->text_color}} !important;"> {{ @$content->title }}</h3></div>
                        <div class="col-lg-8"> <img class="custom-image" src="{{ uploaded_asset(@$content->attachment_id) }}" class="w-100" alt=""></div>
                        <div class="col-lg-4"><div class="custom-pera custom-pera{{ $content->id }}" style="color: {{@$content->text_color}} !important;">{!! $content->description !!}</div></div>
                    </div>
                </div> 
            @elseif($content->style == 5)
                <div class="container">
                    <div class="row gap-12x">
                        <div class="col-lg-12"><h3 class="custom-title" style="color: {{@$content->text_color}} !important;">{{ @$content->title }}</h3></div>
                        <div class="col-lg-4"> <img class="custom-image" src="{{ uploaded_asset(@$content->attachment_id) }}" class="w-100" alt=""></div>
                        <div class="col-lg-8"><div class="custom-pera custom-pera{{ $content->id }}" style="color: {{@$content->text_color}} !important;">{!! $content->description !!}</div></div>
                    </div>
                </div> 
            @elseif($content->style == 6)
                <div class="container">
                    <div class="started-screen-padding">
                        <div class="row align-items-center">
                            <div class="col-lg-5 col-12 mb-4">
                                <div class="started-page-title">
                                    <h3>
                                        {{ $content->title }}
                                    </h3>
                                </div>
                                <div class="started-page-content">
                                    <p>{{ $content->description }}</p>
                                </div>
                                <div class="d-flex started-page-btn flex-wrap">
                                    @if ($content->order == 0)
                                        <a class="primary_btn started-btn" href="{{ route('saas.pricingPage') }}">{{ _trans('frontend.Get Started') }}</a>
                                    @endif
                                    @if ($content->link)
                                        <a class="watch-demo-btn" href="{{ $content->link }}" target="_blank">
                                            <i class="watch-demo-icon fa-solid fa-circle-play"></i> 
                                            <span>{{ _trans('frontend.Watch Demo') }}</span>
                                        </a>
                                    @endif                                    
                                </div>
                            </div>
                            <div class="col-lg-7 col-12">
                                <div class="started-page-image">
                                    <img src="{{ uploaded_asset(@$content->attachment_id) }}" alt="img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($content->style == 7)
                <div class="container">
                    <div class="started-screen-padding">
                        <div class="row align-items-center">
                            <div class="col-lg-7 col-12">
                                <div class="started-page-image">
                                    <img src="{{ uploaded_asset(@$content->attachment_id) }}" alt="img">
                                </div>
                            </div>
                            <div class="col-lg-5 col-12 mb-4">
                                <div class="started-page-title">
                                    <h3>
                                        {{ $content->title }}
                                    </h3>
                                </div>
                                <div class="started-page-content">
                                    <p>{{ $content->description }}</p>
                                </div>
                                <div class="d-flex started-page-btn flex-wrap">
                                    @if ($content->order == 0)
                                        <a class="primary_btn started-btn" href="{{ route('saas.pricingPage') }}">{{ _trans('frontend.Get Started') }}</a>
                                    @endif
                                    
                                    @if ($content->link)
                                        <a class="watch-demo-btn" href="{{ $content->link }}" target="_blank">
                                            <i class="watch-demo-icon fa-solid fa-circle-play"></i> 
                                            <span>{{ _trans('frontend.Watch Demo') }}</span>
                                        </a>
                                    @endif                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif 
        </section>


        @push('scripts')
            <script>
                $('.custom-pera{{ $content->id }}').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }} > p').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > h1').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > h2').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > h3').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > h4').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > h5').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > h6').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > span').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > div').css('color', '{{@$content->text_color}}');
                $('.custom-pera{{ $content->id }}  > i').css('color', '{{@$content->text_color}}');
            </script>
        @endpush
    @endforeach
@endsection
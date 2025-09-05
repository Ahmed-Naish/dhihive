@extends('saas::frontend.layouts.master')

@section('title', @$data['title'])

@section('content')
    <div class="without-bg-page-screen gradiant-manage-work">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="mx-width-600 mx-auto">
                        <div class="without-bg-page-title">
                            <h3>
                                {{ _trans('frontend.Manage your work with powerful HRM Software') }}
                            </h3>
                        </div>
                        <div class="without-bg-page-content">
                            <p>
                                {{ _trans('frontend.These important features help in effective management of clients, both existing and prospective') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="ot-feature-nav">
                        <ul class="nav ot-feature-nav-pills nav-pills mb-5" id="pills-tab" role="tablist">
                            @foreach ($data['planFeatures'] ?? [] as $planFeature)
                                <li class="nav-item" role="presentation">
                                    <button 
                                        class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                        id="pills-{{ $planFeature->id }}-tab" 
                                        data-bs-toggle="pill"
                                        data-bs-target="#pills-{{ $planFeature->id }}" 
                                        type="button" 
                                        role="tab"
                                        aria-controls="pills-{{ $planFeature->id }}" 
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        {{ $planFeature->title }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            @foreach ($data['planFeatures'] ?? [] as $planFeature)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pills-{{ $planFeature->id }}" role="tabpanel" aria-labelledby="pills-{{ $planFeature->id }}-tab">
                                    <div class="ot-feature-box">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="ot-feature-title">
                                                    <h3>{{ $planFeature->heading }}</h3>
                                                </div>
                                                <div class="ot-feature-content">
                                                    <p>{{ $planFeature->short_description }}</p>
                                                </div>
                                                <div class="row">
                                                    {!! $planFeature->description !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <img src="{{ uploaded_asset(@$planFeature->image_id) }}" alt="img">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="with-bg-screen-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="with-bg-page-title">
                        <h3 class="font-700">
                            {{ _trans('frontend.We\'re Ready for a Challenging Project') }}
                        </h3>
                    </div>
                    <div class="with-bg-page-content">
                        <p>{{ _trans('frontend.Send us your brief and tell your project too') }} {{ _trans('frontend.We are ready to help all you need') }}</p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a class="primary_btn white-bg" href="{{ route('saas.pricingPage') }}">{{ _trans('frontend.Get Started') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
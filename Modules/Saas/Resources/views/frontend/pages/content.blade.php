@extends('saas::frontend.layouts.master')

@section('title', @$data['title'])

@section('content')
    <div class="without-bg-page-screen gradiant-manage-work">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    {!! $data['content_show']->content !!}
                </div> 
            </div>
        </div>
    </div>
@endsection

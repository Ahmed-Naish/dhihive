@if(@$data['breadcrumbs'] !="")


<div class="breadcrumb_area bradcam_bg_8">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-8">
                <div class="breadcam_wrap text-center">
                    <h3>{{ @$data['title'] }}</h3>
                    <div class="custom_breadcam">

                        @foreach($data['breadcrumbs'] as $breadcrumb)
                        <a class=" breadcrumb-item {{ @$breadcrumb['is_last']==1?'active':'' }}"
                            href="{{ @$breadcrumb['path'] }}">{{ @$breadcrumb['title'] }}</a>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
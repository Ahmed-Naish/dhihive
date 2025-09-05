@extends('backend.layouts.app')
@section('title', @$title)
<link rel="stylesheet" href="https://bgrins.github.io/spectrum/spectrum.css">
<style>
    .pic-color-icon {
        position: absolute;
        right: 10px;
        top: 6px;
        font-size: 22px;
        color: #9b9b9b;
        cursor: pointer;
        z-index: 1;
        padding: 5px;

    }

    .changingColor {
        font-size: 16px
    }

    .show-color {
        display: flex;
        gap: 10px;
        font-size: 15px;
    }

    .input-box-color-pic {
        height: 50px;
        border: 1px solid var(--ot-border-primary) !important;
        border-radius: 5px;
    }

    .input-box-color-pic {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sp-dd {
        display: none;
    }

    .show-color {
        position: absolute;
        left: 10px;
        top: 6px;
        font-size: 22px;
        cursor: pointer;
        z-index: 1;
        padding: 5px;
    }

    .sp-replacer {
        background: none;
        border: 0;
    }

    .colorBox {
        display: inline-block;
        vertical-align: middle;
        margin: 2px;
        width: 20px;
        height: 20px;
        border: 1px solid #ddd;
        border-radius: 3px;
        box-sizing: border-box;
    }

    .sp-replacer.sp-light {
        position: absolute;
        right: 0;
        top: 9px;
    }

    .sp-preview {
        width: 20px;
        border: 0;
    }

    /* .sp-preview-inner {
        background-color: red !important
    } */
</style>
@section('content')
    {!! breadcrumb([
        'title' => @$title,
        route('admin.dashboard') => _trans('common.Dashboard'),
        '#' => @$title,
    ]) !!}

    <form action="" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card border-0 mb-3">
            <div class="card-body border-0">
                <div class="row">
                    <div class="col-lg-13">
                        <div class="card-title mb-20">
                            <h3>{{ _trans('settings.Logo & Font Family') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- App Name -->
                    <div class="col-lg-4 col-md-6">
                        <div class="col-md-12 form-group mb-3">
                            <label class="form-label">{{ _trans('settings.App Name') }}</label>
                            <input type="text" class="form-control ot-form-control ot-input" name="app_name"
                                   value="{{ @$brandings['app_name'] }}">
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label">{{ _trans('settings.Font Family') }}</label>
                        <select name="font_family" id="" class="select2">
                            @foreach($fontFamilies ?? [] as $font)
                                <option {{ @$brandings['font_family'] == $font ? 'selected' : '' }} value="{{ $font }}">
                                    {{ $font }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-3">
                        <label class="form-label">{{ _trans('settings.Logo') }}</label>
                        <div class="ot_fileUploader left-side mb-3">
                            <input class="form-control" type="text"
                                   placeholder="{{ _trans('settings.Logo') }}"
                                   name="" readonly="" id="placeholder">
                            <div class="primary-btn-small-input">
                                <label class="btn btn-lg ot-btn-primary"
                                       for="fileBrouse">{{ _trans('common.Browse') }}</label>
                                <input type="file" class="d-none form-control"
                                       name="logo" id="fileBrouse" accept="image/*">
                            </div>
                        </div>
                        @if ($errors->has('logo'))
                            <div class="invalid-feedback d-block">
                                {{ $errors->first('logo') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="table-content table-basic mb-20">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Primary Color -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Primary Color') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['primaryColor'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['primaryColor'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="primaryColor"
                                           value="{{ @$brandings['primaryColor'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Primary Light Color') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['primaryLight'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['primaryLight'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="primaryLight"
                                           value="{{ @$brandings['primaryLight'] }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Primary Dark Color') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['primaryDark'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['primaryDark'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="primaryDark"
                                           value="{{ @$brandings['primaryDark'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Card Background Default -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Card Background Default') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['cardBackgroundDefault'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['cardBackgroundDefault'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="cardBackgroundDefault"
                                           value="{{ @$brandings['cardBackgroundDefault'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Card Background Subdued -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Card Background Subdued') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['cardBackgroundSubdued'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['cardBackgroundSubdued'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="cardBackgroundSubdued"
                                           value="{{ @$brandings['cardBackgroundSubdued'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Primary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Primary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textPrimary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textPrimary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textPrimary"
                                           value="{{ @$brandings['textPrimary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Secondary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Secondary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textSecondary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textSecondary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textSecondary"
                                           value="{{ @$brandings['textSecondary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Tertiary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Tertiary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textTertiary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textTertiary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textTertiary"
                                           value="{{ @$brandings['textTertiary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Hint -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Hint') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textHint'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textHint'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textHint"
                                           value="{{ @$brandings['textHint'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Disabled -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Disabled') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textDisabled'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textDisabled'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textDisabled"
                                           value="{{ @$brandings['textDisabled'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Inverse Primary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Inverse Primary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textInversePrimary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textInversePrimary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textInversePrimary"
                                           value="{{ @$brandings['textInversePrimary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Inverse Secondary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Inverse Secondary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textInverseSecondary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textInverseSecondary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textInverseSecondary"
                                           value="{{ @$brandings['textInverseSecondary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Text Inverse Tertiary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Text Inverse Tertiary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['textInverseTertiary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['textInverseTertiary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="textInverseTertiary"
                                           value="{{ @$brandings['textInverseTertiary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Brand Text Link -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Brand Text Link') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['brandTextLink'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['brandTextLink'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="brandTextLink"
                                           value="{{ @$brandings['brandTextLink'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Primary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Primary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconPrimary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconPrimary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconPrimary"
                                           value="{{ @$brandings['iconPrimary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Secondary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Secondary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconSecondary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconSecondary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconSecondary"
                                           value="{{ @$brandings['iconSecondary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Disabled -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Disabled') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconDisabled'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconDisabled'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconDisabled"
                                           value="{{ @$brandings['iconDisabled'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Inverse -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Inverse') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconInverse'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconInverse'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconInverse"
                                           value="{{ @$brandings['iconInverse'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Accent -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Accent') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconAccent'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconAccent'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconAccent"
                                           value="{{ @$brandings['iconAccent'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Navigation Bar -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Navigation Bar') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconNavigationBar'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconNavigationBar'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconNavigationBar"
                                           value="{{ @$brandings['iconNavigationBar'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Icon Accent Inactive -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Icon Accent Inactive') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['iconAccentInactive'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['iconAccentInactive'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="iconAccentInactive"
                                           value="{{ @$brandings['iconAccentInactive'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Divider Primary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Divider Primary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['dividerPrimary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['dividerPrimary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="dividerPrimary"
                                           value="{{ @$brandings['dividerPrimary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Divider Inverse -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Divider Inverse') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['dividerInverse'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['dividerInverse'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="dividerInverse"
                                           value="{{ @$brandings['dividerInverse'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Button Default -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Button Default') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['buttonDefault'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['buttonDefault'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="buttonDefault"
                                           value="{{ @$brandings['buttonDefault'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Button Secondary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Button Secondary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['buttonSecondary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['buttonSecondary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="buttonSecondary"
                                           value="{{ @$brandings['buttonSecondary'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Button Inverse -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Button Inverse') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['buttonInverse'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['buttonInverse'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="buttonInverse"
                                           value="{{ @$brandings['buttonInverse'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Button Disabled -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Button Disabled') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['buttonDisabled'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['buttonDisabled'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="buttonDisabled"
                                           value="{{ @$brandings['buttonDisabled'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Container -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Container') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['container'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['container'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="container"
                                           value="{{ @$brandings['container'] }}">
                                </div>
                            </div>
                        </div>

                        <!-- Container Secondary -->
                        <div class="col-lg-4 col-md-6">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label">{{ _trans('settings.Container Secondary') }}</label>
                                <div class="input-box-color-pic position-relative">
                                    <div class="show-color">
                                        <div class="colorBox"
                                             style="background-color: {{ @$brandings['containerSecondary'] }}"></div>
                                        <div class="changingColor colorValueDisplay">{{ @$brandings['containerSecondary'] }}</div>
                                    </div>
                                    <input class="preferredHex form-control" name="containerSecondary"
                                           value="{{ @$brandings['containerSecondary'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if (hasPermission('branding_update'))
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-gradian mr-2">
                                    <span class="w-100"></span> {{ _trans('common.Update') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')

    <script src="https://bgrins.github.io/spectrum/spectrum.js"></script>
    <script>
        $(".preferredHex").spectrum({
            preferredFormat: "hex",
            showInput: true,
            showPalette: false,
        });


        $(document).ready(function () {
            $(".preferredHex").on('change', function () {
                var colorVal = $(this).val();
                var parentBox = $(this).closest('.input-box-color-pic');
                var colorDisplay = parentBox.find('.colorValueDisplay');
                var colorBox = parentBox.find('.colorBox');

                colorDisplay.css('color', colorVal).text(colorVal);
                colorBox.css('background-color', colorVal);
            });
        });
    </script>
@endsection

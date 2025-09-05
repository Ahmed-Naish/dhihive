<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="{{ company_fav_icon(@base_settings('company_icon')) }}" type="image/x-icon" >
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <title>{{ _trans('common.Deactivated Company') }} - {{ @base_settings('company_name') }}</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lexend', sans-serif;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #fffafa;
            width: 100%;
            height: 100vh;
            padding: 10px;
        }

        .heading {
            display: flex;
            align-items:baseline;
            gap: 10px;
        }

        .heading img {
            width: 70px;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #E62E2E
        }

        a {
            color: #2563eb;
            font-weight: 700;
        }

        @media screen and (max-width: 576px) {
            .heading img {
                width: 48px;
            }

            .title {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="heading">
            <img src="{{ asset('assets/images/oops.png') }}" alt="">
            <h2 class="title">{{ _trans('common.Company Deactivated') }}</h2>
        </div>
        <p>
            {{ _trans('common.Sorry, you are currently unable to access the panel as the company account is deactivated') }}. {{ _trans('common.Please contact your system administrator ') }}
            <a href="{{ env('APP_URL') }}" target="_blank">
                {{ @base_settings('company_name') }}
            </a>
            {{ _trans('common.for assistance') }}.
        </p>
    </div>
</body>
</html>
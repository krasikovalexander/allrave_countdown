<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #f5f8fa;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }
            body {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                text-shadow: 1px 1px 1px rgba(0,0,0,0.004);
            }

            .full-height {
                height: 100vh;
            }

            .full-width {
                width: 100vw;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 18px;
                top: 18px;
            }

            .content {
                //text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 6px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none!important;
                text-transform: uppercase;
                text-shadow: 1px 1px #e5e5e5;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            #main {
                background-color: #f5f8fa;
            }
            .fullscreen {
                background-image: url(/images/enter-and-exit-fullscreen.png);
                display: inline-block;
                width: 24px;
                height: 24px;
                background-position: 0 0;
                padding: 0!important;
                background-size: 92px 54px;
                cursor: pointer;
                float: right;
                opacity: 0.8;
            }
            .fullscreen:hover, .fullscreen:active {
                opacity:1;
            }
        </style>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="/js/jquery.min.js"></script>

    <link href="{{ asset('css/jquery-confirm.min.css') }}" rel="stylesheet">
    <!-- Scripts -->
    <script src="{{ asset('js/jquery-confirm.min.js') }}"></script>
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/nosleep.min.js') }}"></script>
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    @yield('styles')
    @yield('scripts')
</head>
<body>
    <div id='main' class="flex-center position-ref full-height full-width">

        @if (Route::has('login'))
            <div class="top-right links">
                <a href="/">Kiosk mode</a>
                @if (Auth::check())
                    <a href="{{ url('/home') }}">Events</a>
                    @if (Auth::user()->isAdmin())
                        <a href="{{ url('/admin') }}">Admin</a>
                    @endif
                    <a href="{{ url('/logout') }}">Logout</a>
                    <!--<a class='fullscreen' onclick="fullscreen()"></a>-->
                @else
                    <a href="{{ url('/login') }}">Login</a>
                @endif
            </div>
        @endif

        <div class="content">
            <div id="app">
                @yield('content')
            </div>
        </div>
    </div>


</body>
</html>

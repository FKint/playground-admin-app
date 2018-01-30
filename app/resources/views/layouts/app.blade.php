<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">

    <title>
        @yield('title', 'Home') - Playground Administration
    </title>

    <link href="{{mix('/css/app.css')}}" rel="stylesheet">
    <style>
        body {
            padding-top: 60px;
        }

        .twitter-typeahead {
            width: 100%;
        }

        #top-navbar {
            background-color: {{ env('ENVIRONMENT_COLOR') }};
        }
    </style>
    @stack('styles')

</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top" id="top-navbar">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">{{ env('ENVIRONMENT_DESCRIPTION') }}</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                @section('navbar-items')
                    <li>
                        <a href="{{route('login')}}">Log in</a>
                    </li>
                @endsection
                @yield('navbar-items')
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
@include('modals.helpers')
@stack('modals')

<div class="container" role="main">

    @yield('content')

</div> <!-- /container -->
<script>
    window.Laravel = {!!  json_encode([
        'csrfToken' => csrf_token(),
    ])  !!};
</script>
<script src="{{mix('/js/manifest.js')}}"></script>
<script src="{{mix('/js/vendor.js')}}"></script>
<script src="{{mix('/js/app.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function formatPriceWithoutSign(val) {
        return parseFloat(val).toFixed(2);
    }

    function formatPrice(val) {
        return "€&nbsp;" + formatPriceWithoutSign(val);
    }
</script>
@stack('scripts')
</body>
</html>
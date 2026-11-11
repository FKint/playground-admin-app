<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/favicon.ico">

    <title>
        @yield('title', 'Home') - Playground Administration
    </title>

    @vite('resources/assets/js/app.js')
    <style>
        #top-navbar {
            background-color: {{ env('ENVIRONMENT_COLOR') }};
        }
    </style>
    @stack('styles')

</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-expand-md navbar-dark fixed-top" id="top-navbar">
    <div class="container">
        <a class="navbar-brand"
           href="{{route('home')}}">@yield('year-descriptor', env('ENVIRONMENT_DESCRIPTION'))</a>
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbar"
                aria-expanded="false" aria-controls="navbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="navbar-nav me-auto">
                @section('navbar-items')
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('login')}}">Log in</a>
                    </li>
                @endsection
                @yield('navbar-items')
            </ul>
        </div>
    </div>
</nav>
@include('modals.helpers')
@stack('modals')

<div class="container" role="main">

    @yield('content')

</div> <!-- /container -->
<div class="container">
    Version info: 
    <a href="{{ $app_version['github_link'] }}">
        {{ $app_version['sha1'] }} ({{ $app_version['timestamp'] }})
    </a>
</div>
<script>
    window.Laravel = {!!  json_encode([
        'csrfToken' => csrf_token(),
    ])  !!};
</script>


<script type="module">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    window.formatPriceWithoutSign = function (val) {
        return parseFloat(val).toFixed(2);
    };

    window.formatPrice = function (val) {
        return "€&nbsp;" + window.formatPriceWithoutSign(val);
    };
</script>
@stack('scripts')
</body>
</html>
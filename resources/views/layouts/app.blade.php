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

    <title>Playground Admin App</title>

    <link href="{{mix('/css/app.css')}}" rel="stylesheet">
    <style>
        body {
            padding-top: 60px;
        }

        .twitter-typeahead {
            width: 100%;
        }
        .table {
            table-layout: fixed;
            word-wrap: break-word;
        }
    </style>
    @stack('styles')

</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Bootstrap theme</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li @if(!empty($selected_menu_item) && $selected_menu_item == 'dashboard')class="active"@endif><a href="{{route('dashboard')}}">Dashboard</a></li>
                <li @if(!empty($selected_menu_item) && $selected_menu_item == 'children')class="active"@endif><a href="{{route('children')}}">Kinderen</a></li>
                <li @if(!empty($selected_menu_item) && $selected_menu_item == 'families')class="active"@endif><a href="{{route('families')}}">Gezinnen</a></li>
                <li @if(!empty($selected_menu_item) && $selected_menu_item == 'registrations')class="active"@endif><a href="{{route('registrations')}}">Registraties</a></li>
                <li @if(!empty($selected_menu_item) && $selected_menu_item == 'lists')class="active"@endif><a href="#">Lijsten</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Extra<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{route('settings')}}">Instellingen</a></li>
                        <li><a href="#">Uitloggen</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
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
        return "€ " + formatPriceWithoutSign(val);
    }
</script>
@stack('scripts')
</body>
</html>
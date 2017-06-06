@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Start</div>

                    <div class="panel-body">
                        @if(isset($user))
                            U bent ingelogd.
                            @if($user->admin)
                                <div class="alert alert-warning">U hebt geen toestemming om de applicatie te
                                    gebruiken.
                                </div>
                            @else
                                <div class="alert alert-success">Ga naar
                                    <a href="{{ route('dashboard') }}">het dashboard</a>.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                U bent niet ingelogd.
                                <a href="{{ route('login') }}">Log in</a> of <a href="{{ route('register') }}">registreer</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

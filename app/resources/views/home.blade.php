@extends('layouts.app')

@section('title')
    Home
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">Start</div>

                    <div class="card-body">
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

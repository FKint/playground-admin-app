@extends('layouts.app')

@section('title')
    Home
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card ">
                    <div class="card-header">Start</div>

                    <div class="card-body">
                        U heeft toegang tot de volgende jaargangen:
                        <ul>
                            @foreach(Auth::user()->years() as $year)
                                <li>
                                    <a href="{{route('internal.dashboard', ['year' => $year])}}">
                                        {{$year->description}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

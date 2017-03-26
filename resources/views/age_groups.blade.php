@extends('layouts.app')

@section('content')
    <div>
        <ul>
            @foreach($age_groups as $age_group)
                <li>
                    <ul>
                        <li><b>Abbreviation: </b> {{$age_group->abbreviation}}</li>
                        <li><b>Name: </b> {{$age_group->name}}</li>
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
@extends('layouts.app')

@section('content')
    <h1>Instellingen</h1>
    <div class="row">
        <div class="col-md-6">
            <h2>Werkingen</h2>
            @include('settings.age_groups')
        </div>
    </div>
@stop
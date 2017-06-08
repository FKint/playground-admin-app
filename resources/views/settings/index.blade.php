@extends('layouts.app')

@section('title')
    Instellingen
@endsection
@section('content')
    <h1>Instellingen</h1>
    <div class="row">
        <div class="col-xs-12">
            <h2>Werkingen</h2>
            @include('settings.age_groups')
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h2>Extraatjes</h2>
            @include('settings.supplements')
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h2>Dagdelen</h2>
            @include('settings.day_parts')
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h2>Tariefplannen</h2>
            @include('settings.tariffs')
        </div>
    </div>
@stop
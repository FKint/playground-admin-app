@extends('layouts.internal')

@section('title')
    Instellingen
@endsection
@section('content')
    <h1 class="mt-4">Instellingen</h1>
    <div class="row">
        <div class="col-12">
            <h2 class="mt-4">Werkingen</h2>
            @include('settings.age_groups')
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2 class="mt-4">Extraatjes</h2>
            @include('settings.supplements')
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2 class="mt-4">Dagdelen</h2>
            @include('settings.day_parts')
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2 class="mt-4">Tariefplannen</h2>
            @include('settings.tariffs')
        </div>
    </div>
@stop
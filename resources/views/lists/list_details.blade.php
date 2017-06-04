@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-xs-12 col-lg-6">
            <h2>Details</h2>
            @include('lists.list_details.settings')
        </div>
        <div class="col-xs-12 col-lg-6">
            <h2>Deelnemers</h2>
            @include('lists.list_details.participants')
        </div>
    </div>
@endsection
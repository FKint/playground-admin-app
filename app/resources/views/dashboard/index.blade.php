@extends('layouts.internal')

@section('title')
    Dashboard
@endsection
@section('content')
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Kassa</h3>
                </div>
                <div class="panel-body">
                    @include('dashboard.admin_sessions')
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Registraties vandaag</h3>
                </div>
                <div class="panel-body">
                    @include('dashboard.day_overview')
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Lijsten</h3>
                </div>
                <div class="panel-body">
                    @include('dashboard.lists_overview')
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Registraties overzicht</h3>
                </div>
                <div class="panel-body">
                    @include('dashboard.summer_overview')
                </div>
            </div>
        </div>
    </div>
@endsection
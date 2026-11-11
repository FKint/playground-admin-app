@extends('layouts.internal')

@section('title')
    Dashboard
@endsection
@section('content')
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Kassa</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.admin_sessions')
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Registraties vandaag</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.day_overview')
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Lijsten</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.lists_overview')
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Registraties overzicht</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.summer_overview')
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.internal')

@section('title')
    Dashboard
@endsection
@section('content')
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kassa</h3>
                </div>
                <div class="card-body">
                    @include('dashboard.admin_sessions')
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registraties vandaag</h3>
                </div>
                <div class="card-body">
                    @include('dashboard.day_overview')
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lijsten</h3>
                </div>
                <div class="card-body">
                    @include('dashboard.lists_overview')
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Registraties overzicht</h3>
                </div>
                <div class="card-body">
                    @include('dashboard.summer_overview')
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.internal')

@section('title')
    Dashboard
@endsection
@section('content')
    <h1 class="mt-4">Dashboard</h1>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Kassa</h5>
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
                    <h5 class="card-title mb-0">Registraties vandaag</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.day_overview')
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lijsten</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.lists_overview')
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Registraties overzicht</h5>
                </div>
                <div class="card-body">
                    @include('dashboard.summer_overview')
                </div>
            </div>
        </div>
    </div>
@endsection
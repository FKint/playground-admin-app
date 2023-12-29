@extends('layouts.internal')
@section('title')
    Kassasessie wijzigen
@endsection
@section('content')
    <h1>Kassasessie wijzigen</h1>
    {{ html()->modelForm($admin_session)->class('form-horizontal')->open() }}
    @include('forms.admin_session')
    {{ html()->closeModelForm() }}
@endsection
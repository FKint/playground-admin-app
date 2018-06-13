@extends('layouts.internal')
@section('title')
    Kassasessie wijzigen
@endsection
@section('content')
    <h1>Kassasessie wijzigen</h1>
    {{ Form::model($admin_session, ['class' => 'form-horizontal']) }}
    @include('forms.admin_session')
    {{ Form::close() }}
@endsection
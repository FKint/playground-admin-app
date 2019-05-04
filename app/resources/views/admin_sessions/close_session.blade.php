@extends('layouts.internal')
@section('title')
    Kassasessie afsluiten
@endsection
@section('content')
    <h3>Kassa afsluiten</h3>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'closes-admin-session-form', 'dusk' => 'close-admin-session-form']) }}
    @include('forms.admin_session')
    {{ Form::close() }}
@endsection
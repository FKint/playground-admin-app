@extends('layouts.app')

@section('content')
    <h3>Kassa afsluiten</h3>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'closes-admin-session-form']) }}
    @include('forms.admin_session')
    {{ Form::close() }}
@endsection
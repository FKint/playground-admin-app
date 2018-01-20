@extends('layouts.app')
@section('title')
    Kassasessie wijzigen
@endsection
@section('content')
    <h1>Kassasessie wijzigen</h1>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{ Form::model($admin_session, ['class' => 'form-horizontal']) }}
    @include('forms.admin_session')
    {{ Form::close() }}
@endsection
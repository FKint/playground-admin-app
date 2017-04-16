@extends('layouts.app')

@section('content')

    <h1>Nieuw gezin toevoegen</h1>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-family-form']) }}
    @include('families.forms.family')
    {{ Form::close() }}

@endsection

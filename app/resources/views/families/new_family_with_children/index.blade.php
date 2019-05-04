@extends('layouts.internal')
@section('title')
    Nieuwe voogd
@endsection
@section('content')

    <h1>Nieuwe voogd toevoegen</h1>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-family-form', 'dusk' => 'new-family-form']) }}
    @include('forms.family', ['submit_text' => 'Voogd aanmaken'])
    {{ Form::close() }}

@endsection

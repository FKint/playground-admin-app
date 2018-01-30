@extends('layouts.internal')
@section('title')
    Nieuwe lijst maken
@endsection
@section('content')
    <h1>Nieuwe lijst maken</h1>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-list-form']) }}
    @include('forms.list', ['with_id'=> false])
    {{ Form::close() }}
@endsection
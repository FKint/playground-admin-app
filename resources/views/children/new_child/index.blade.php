@extends('layouts.app')

@section('content')

<h1>Nieuw kind toevoegen</h1>
{{ Form::open(['class' => 'form-horizontal', 'id' => 'new-child-form']) }}
@include('children.forms.child')
{{ Form::close() }}

@endsection

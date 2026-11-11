@extends('layouts.internal')
@section('title')
    Nieuwe lijst maken
@endsection
@section('content')
    <h1 class="mt-4">Nieuwe lijst maken</h1>
    {{ html()->form()->id('new-list-form')->attributes(['dusk' => 'new-list-form'])->open() }}
    <x-form-contents.activity-list />
    {{ html()->form()->close() }}
@endsection
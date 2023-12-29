@extends('layouts.internal')
@section('title')
    Nieuwe lijst maken
@endsection
@section('content')
    <h1>Nieuwe lijst maken</h1>
    {{ html()->form()->class('form-horizontal')->id('new-list-form')->attributes(['dusk' => 'new-list-form'])->open() }}
    <x-form-contents.activity-list />
    {{ html()->form()->close() }}
@endsection
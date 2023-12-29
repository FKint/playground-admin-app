@extends('layouts.internal')
@section('title')
    Nieuwe voogd
@endsection
@section('content')

    <h1>Nieuwe voogd toevoegen</h1>
    {{ html()->form()->class('form-horizontal')->id('new-family-form')->attributes(['dusk' => 'new-family-form'])->open() }}
    <x-form-contents.family submit-text="Voogd aanmaken" />
    {{ html()->form()->close() }}

@endsection

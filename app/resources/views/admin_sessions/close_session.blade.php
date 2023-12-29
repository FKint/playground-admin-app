@extends('layouts.internal')
@section('title')
    Kassasessie afsluiten
@endsection
@section('content')
    <h3>Kassa afsluiten</h3>
    {{ html()->form()->id('closes-admin-session-form')->class('form-horizontal')->attributes(['dusk' => 'close-admin-session-form'])->open() }}
    <x-form-contents.admin-session />
    {{ html()->form()->close() }}
@endsection
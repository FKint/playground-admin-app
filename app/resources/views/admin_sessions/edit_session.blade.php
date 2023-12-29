@extends('layouts.internal')
@section('title')
    Kassasessie wijzigen
@endsection
@section('content')
    <h1>Kassasessie wijzigen</h1>
    {{ html()->modelForm($admin_session)->class('form-horizontal')->open() }}
    <x-form-contents.admin-session />
    {{ html()->closeModelForm() }}
@endsection
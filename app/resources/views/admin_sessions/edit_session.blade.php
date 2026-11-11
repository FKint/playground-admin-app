@extends('layouts.internal')
@section('title')
    Kassasessie wijzigen
@endsection
@section('content')
    <h1 class="mt-4">Kassasessie wijzigen</h1>
    {{ html()->modelForm($admin_session)->open() }}
    <x-form-contents.admin-session />
    {{ html()->closeModelForm() }}
@endsection
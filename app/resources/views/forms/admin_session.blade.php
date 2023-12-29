@extends('forms.form')

@php
    $isReadOnly = (isset($readonly) && $readonly);
@endphp

@section('form-content')
    <x-form-elements.text name="responsible_name" display-name="Naam verantwoordelijke" :readonly="$isReadOnly" />
    <x-form-elements.number name="counted_cash" display-name="Kassatelling" :readonly="$isReadOnly" pattern="[0-9]+([\\.,][0-9]+)?" step="0.01" />
    <x-form-elements.text-area name="remarks" display-name="Opmerkingen" :readonly="$isReadOnly" />
    @if(!$isReadOnly)
        <x-form-elements.submit />
    @endif
@endsection
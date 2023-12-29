{{-- Parameters: Year $year --}}
@extends('forms.form')

@php
    $isReadOnly = (isset($readonly) && $readonly);
@endphp

@section('form-content')
    <x-form-elements.text name="first_name" display-name="Voornaam" :readonly="$isReadOnly" />
    <x-form-elements.text name="last_name" display-name="Naam" :readonly="$isReadOnly" />
    <x-form-elements.number name="birth_year" display-name="Geboortejaar" :readonly="$isReadOnly" />
    <x-form-elements.dropdown name="age_group_id" display-name="Werking"
        :choices="['0' => 'Werking'] + $year->getAllAgeGroupsById()" :readonly="$isReadOnly" />
    <x-form-elements.text-area name="remarks" display-name="Opmerkingen" :readonly="$isReadOnly" />
    @if(!$isReadOnly)
        <x-form-elements.submit />
    @endif
@endsection
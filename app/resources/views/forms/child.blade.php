{{-- Parameters: Year $year --}}
@extends('forms.form')

@section('form-content')
    {{ Form::bsText('first_name', 'Voornaam', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsText('last_name', 'Naam', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsNumber('birth_year', 'Geboortejaar', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsDropdown('age_group_id', 'Werking', ['0' => 'Werking'] + $year->getAllAgeGroupsById(), (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('remarks', 'Opmerkingen', (isset($readonly) && $readonly)?['readonly']:[]) }}
    @if(!isset($readonly) || !$readonly)
        {{ Form::bsSubmit() }}
    @endif
@endsection
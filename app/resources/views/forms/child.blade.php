{{-- Parameters: Year $year --}}
@extends('forms.form')

@section('form-content')
    {{ Form::bsText('first_name', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsText('last_name', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsNumber('birth_year', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsDropdown('age_group_id', null, ['0' => 'Werking'] + $year->getAllAgeGroupsById(), (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('remarks', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    @if(!isset($readonly) || !$readonly)
        {{ Form::bsSubmit() }}
    @endif
@endsection
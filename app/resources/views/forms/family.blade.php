@extends('forms.form')

@section('form-content')
    @if(isset($with_id) && $with_id)
        {{ Form::bsText('id', null, ['readonly']) }}
    @endif
    {{ Form::bsText('guardian_first_name', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsText('guardian_last_name', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsDropdown('tariff_id', null, $year->getAllTariffsById(), (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('remarks', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('contact', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('social_contact', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsCheckbox('needs_invoice', null, (isset($readonly) && $readonly)?['readonly']:[]) }}
    @if(!isset($readonly) || !$readonly)
        {{ Form::bsSubmit(isset($submit_text) ? $submit_text : "Opslaan") }}
    @endif
@endsection
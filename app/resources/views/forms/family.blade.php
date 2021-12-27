@extends('forms.form')

@section('form-content')
    @if(isset($with_id) && $with_id)
        {{ Form::bsText('id', null, ['readonly']) }}
    @endif
    {{ Form::bsText('guardian_first_name', 'Voornaam', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsText('guardian_last_name', 'Naam', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsDropdown('tariff_id', 'Tarief', $year->getAllTariffsById(), (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsText('email', 'E-mail', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('remarks', 'Opmerkingen', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('contact', 'Contactgegevens', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsTextarea('social_contact', 'Contact sociaal tarief', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsForcedChoiceRadio('needs_invoice', 'Betalingswijze', ['0' => 'Cash', '1' => 'Factuur'], (isset($readonly) && $readonly)?['readonly']:[]) }}
    @if(!isset($readonly) || !$readonly)
        {{ Form::bsSubmit(isset($submit_text) ? $submit_text : "Opslaan") }}
    @endif
@endsection
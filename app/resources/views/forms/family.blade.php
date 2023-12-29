@extends('forms.form')

@php
    $isReadOnly = (isset($readonly) && $readonly);
@endphp

@section('form-content')
    @if(isset($with_id) && $with_id)
        <x-form-elements.text name="id" readonly />
    @endif
    <x-form-elements.text name="guardian_first_name" display-name="Voornaam" :readonly="$isReadOnly" />
    <x-form-elements.text name="guardian_last_name" display-name="Naam" :readonly="$isReadOnly" />
    <x-form-elements.dropdown name="tariff_id" display-name="Tarief"
        :choices="$year->getAllTariffsById()->all()" :readonly="$isReadOnly" />
    <x-form-elements.forced-choice-radio name="needs_invoice" display-name="Betalingswijze"
        :choices="['0' => 'Cash', '1' => 'Factuur']" :readonly="$isReadOnly" />
    <x-form-elements.text name="email" display-name="E-mail" :readonly="$isReadOnly" />
    <x-form-elements.text-area name="remarks" display-name="Opmerkingen" :readonly="$isReadOnly" />
    <x-form-elements.text-area name="contact" display-name="Contactgegevens" :readonly="$isReadOnly" />
    <x-form-elements.text-area name="social_contact" display-name="Contact sociaal tarief" :readonly="$isReadOnly" />
    @if(!$isReadOnly)
        <x-form-elements.submit :text="isset($submit_text) ? $submit_text : 'Opslaan'" />
    @endif
@endsection
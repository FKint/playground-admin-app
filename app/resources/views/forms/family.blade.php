@if(isset($with_id) && $with_id)
    {{ Form::bsText('id', ['readonly']) }}
@endif
{{ Form::bsText('guardian_first_name', (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsText('guardian_last_name', (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsDropdown('tariff_id', $year->getAllTariffsById(), (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsTextarea('remarks', (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsTextarea('contact', (isset($readonly) && $readonly)?['readonly']:[]) }}
@if(!isset($readonly) || !$readonly)
    {{ Form::bsSubmit(isset($submit_text) ? $submit_text : "Opslaan") }}
@endif
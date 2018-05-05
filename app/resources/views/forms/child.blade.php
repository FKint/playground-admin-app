{{-- Parameters: Year $year --}}

{{ Form::bsText('first_name', (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsText('last_name', (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsNumber('birth_year', (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsDropdown('age_group_id', ['0' => 'Werking'] + $year->getAllAgeGroupsById(), (isset($readonly) && $readonly)?['readonly']:[]) }}
{{ Form::bsTextarea('remarks', (isset($readonly) && $readonly)?['readonly']:[]) }}
@if(!isset($readonly) || !$readonly)
    {{ Form::bsSubmit() }}
@endif
{{ Form::bsText('guardian_first_name') }}
{{ Form::bsText('guardian_last_name') }}
{{ Form::bsDropdown('tariff_id', $all_tariffs) }}
{{ Form::bsText('remarks') }}
{{ Form::bsText('contact') }}
{{ Form::bsSubmit() }}
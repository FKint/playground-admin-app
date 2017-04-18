{{ Form::bsText('guardian_first_name') }}
{{ Form::bsText('guardian_last_name') }}
{{ Form::bsDropdown('tariff_id', $all_tariffs_by_id) }}
{{ Form::bsText('remarks') }}
{{ Form::bsText('contact') }}
{{ Form::bsSubmit() }}
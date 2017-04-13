{{ Form::bsText('first_name') }}
{{ Form::bsText('last_name') }}
{{ Form::bsNumber('birth_year') }}
{{ Form::bsDropdown('age_group_id', $all_age_groups) }}
{{ Form::bsText('remarks') }}
{{ Form::bsSubmit() }}
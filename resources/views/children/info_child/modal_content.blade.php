{{ Form::model($child, ['class' => 'form-horizontal', 'id' => 'info-child-form']) }}
{{ Form::bsText('first_name', ['readonly']) }}
{{ Form::bsText('last_name', ['readonly']) }}
{{ Form::bsNumber('birth_year', ['readonly']) }}
{{ Form::bsDropdown('age_group_id', $all_age_groups_by_id, ['readonly']) }}
{{ Form::bsText('remarks', ['readonly']) }}
{{ Form::close() }}
<ul>
    @foreach($child->child_families as $child_family)
        <h4>Voogd {{ $child_family->family->guardian_full_name() }}</h4>
        {{ Form::model($child_family->family, ['class'=>'form-horizontal']) }}
        {{ Form::bsText('id', ['readonly']) }}
        {{ Form::bsText('guardian_first_name', ['readonly']) }}
        {{ Form::bsText('guardian_last_name', ['readonly']) }}
        {{ Form::bsDropdown('tariff_id', $all_tariffs_by_id, ['readonly']) }}
        {{ Form::bsText('remarks', ['readonly']) }}
        {{ Form::bsText('contact', ['readonly']) }}
        {{ Form::close() }}
    @endforeach
</ul>

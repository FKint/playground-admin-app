<ul class="nav nav-tabs" role="tablist" id="info-child-tablist">
    <li role="presentation" class="active">
        <a href="#info-child-info-div" aria-controls="info" role="tab" data-toggle="tab">Info</a>
    </li>
    <li role="presentation">
        <a href="#info-child-families-div" aria-controls="families" role="tab" data-toggle="tab">Voogden</a>
    </li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="info-child-info-div">
        {{ Form::model($child, ['class' => 'form-horizontal', 'id' => 'info-child-form']) }}
        {{ Form::bsText('first_name', ['readonly']) }}
        {{ Form::bsText('last_name', ['readonly']) }}
        {{ Form::bsNumber('birth_year', ['readonly']) }}
        {{ Form::bsDropdown('age_group_id', $all_age_groups_by_id, ['readonly']) }}
        {{ Form::bsText('remarks', ['readonly']) }}
        {{ Form::close() }}
    </div>
    <div role="tabpanel" class="tab-pane" id="info-child-families-div">
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
    </div>
</div>

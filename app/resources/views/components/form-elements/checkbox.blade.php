{{ Form::hidden($name, '0') }}
<div class="form-group">
    {{ Form::label($name, $displayName, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        {{ Form::checkbox($name, '1', null, 
            $attributes->merge(['class' => 'form-control', 'dusk' => $name])->getAttributes()) }}
    </div>
</div>
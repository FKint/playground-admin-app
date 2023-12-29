<div class="form-group">
    {{ Form::label($name, $displayName, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        {{ Form::select($name, $choices, $value, 
            $attributes->merge(['class' => 'form-control', 'dusk' => $name])->getAttributes()) }}
    </div>
</div>
<div class="form-group">
    {{ Form::label($name, $display_name, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        {{ Form::select($name, $choices, $value, $attributes=array_merge(['class' => 'form-control', 'dusk' => $name],
        $attributes)) }}
    </div>
</div>
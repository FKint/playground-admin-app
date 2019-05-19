{{ Form::hidden($name, '0') }}
<div class="form-group">
    {{ Form::label($name, $display_name, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        {{ Form::checkbox($name, '1', null, $attributes=array_merge(['class' => 'form-control', 'dusk' => $name], $attributes)) }}
    </div>
</div>
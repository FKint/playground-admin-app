<div class="form-group">
    {{ Form::label($name, null, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        {{ Form::textarea($name, null, $attributes=array_merge(['class' => 'form-control', 'rows' => 3], $attributes)) }}
    </div>
</div>
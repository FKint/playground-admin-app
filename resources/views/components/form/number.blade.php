<div class="form-group">
    {{ Form::label($name, null, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        {{ Form::number($name, null, $attributes=array_merge(['class' => 'form-control'], $attributes)) }}
    </div>
</div>
<div class="form-group">
    {{ Form::label($name, null, ['class' => 'col-lg-3 control-label']) }}
    <div class="col-lg-9">
        {{ Form::text($name, "", $attributes=array_merge(['class' => 'form-control'], $attributes)) }}
    </div>
</div>
<div class="form-group">
    {{ Form::label($name, $displayName, ['class' => 'col-md-3 control-label']) }}
    <div class="col-md-9">
        @foreach ($choices as $choice_value => $choice_text)
            <div class="form-control">
                {{ Form::radio($name, $choice_value, null, $attributes->merge(['dusk' => $name])->getAttributes()) }}
                {{ $choice_text }}
            </div>
        @endforeach
    </div>
</div>
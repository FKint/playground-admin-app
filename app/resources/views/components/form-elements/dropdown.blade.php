<div class="form-group">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'control-label']) }}
    <div class="col-md-9">
        {{ html()->select(name: $name, options: $choices, value: $value)
            ->class('form-control')
            ->attributes($attributes->merge(['dusk' => $name])->getAttributes()) }}
    </div>
</div>
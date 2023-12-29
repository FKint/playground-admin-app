<div class="form-group">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'control-label']) }}
    <div class="col-md-9">
        {{ html()->input(type: 'number', name: $name, value: $value)
            ->class('form-control')
            ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
            ->isReadOnly($readonly) }}
    </div>
</div>
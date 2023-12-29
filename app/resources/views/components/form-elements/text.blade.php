{{ logger('Readonly: ', [
    'name' => $name,
    'readonly' => $readonly,
    'attributes' => $attributes->getAttributes(),
    ]) }}

<div class="form-group">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'control-label']) }}
    <div class="col-md-9">
        {{ html()->text(name: $name)->class('form-control')
            ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
            ->isReadOnly($readonly) }}
    </div>
</div>
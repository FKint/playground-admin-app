<div class="row mb-3">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'col-form-label', 'text-md-end', 'fw-bold']) }}
    <div class="col-md-9">
        {{ html()->select(name: $name, options: $choices, value: $value)
            ->class('form-select')
            ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
            ->isReadOnly($readonly) }}
    </div>
</div>
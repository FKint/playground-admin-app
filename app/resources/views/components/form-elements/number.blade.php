<div class="row mb-3">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'col-form-label']) }}
    <div class="col-md-9">
        {{ html()->input(type: 'number', name: $name, value: $value)
            ->class('form-control')
            ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
            ->isReadOnly($readonly) }}
    </div>
</div>
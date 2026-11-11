<div class="row mb-3">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'col-form-label']) }}
    <div class="col-md-9">
        {{ html()->textarea(name: $name)->class('form-control')
            ->attributes($attributes->merge(['rows' => 3, 'dusk' => $name])->getAttributes())
            ->isReadOnly($readonly) }}
    </div>
</div>
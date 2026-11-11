{{ html()->hidden(name: $name, value: '0') }}
<div class="row mb-3">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'col-form-label', 'text-md-end', 'fw-bold']) }}
    <div class="col-md-9">
        {{ html()->checkbox(name: $name, value: '1')->class('form-check-input')
                ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
                ->isReadOnly($readonly) }}
    </div>
</div>
<div class="row mb-3">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'col-form-label']) }}
    <div class="col-md-9">
        @foreach ($choices as $choice_value => $choice_text)
            <div class="form-check">
                {{ html()->radio(name: $name, value: $choice_value)->class('form-check-input')
                    ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
                    ->isReadOnly($readonly) }}
                {{ $choice_text }}
            </div>
        @endforeach
    </div>
</div>
<div class="form-group">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'control-label']) }}
    <div class="col-md-9">
        @foreach ($choices as $choice_value => $choice_text)
            <div class="form-control">
                {{ html()->radio(name: $name, value: $choice_value)
                    ->attributes($attributes->merge(['dusk' => $name])->getAttributes())
                    ->isReadOnly($readonly) }}
                {{ $choice_text }}
            </div>
        @endforeach
    </div>
</div>
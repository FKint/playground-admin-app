{{ html()->hidden(name: $name, value: '0') }}
<div class="form-group">
    {{ html()->label(contents: $displayName, for: $name)->class(['col-md-3', 'control-label']) }}
    <div class="col-md-9">
        {{ html()->checkbox(name: $name, value: '1')->class('form-control')
                ->attributes($attributes->merge(['dusk' => $name])->getAttributes()) }}
    </div>
</div>
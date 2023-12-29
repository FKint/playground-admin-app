<div class="form-group">
    <div class="col-md-9 col-md-offset-3">
        {{ Form::submit($text, $attributes->merge(['class' => 'btn pull-right', 'dusk' => 'submit'])->getAttributes()) }}
    </div>
</div>
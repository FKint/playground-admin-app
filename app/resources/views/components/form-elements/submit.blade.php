<div class="form-group">
    <div class="col-md-9 col-md-offset-3">
        {{ html()->submit($text)->class('btn pull-right')
            ->attributes($attributes->merge(['dusk' => 'submit'])->getAttributes()) }}
    </div>
</div>
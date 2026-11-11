<div class="row mb-3">
    <div class="col-md-9 offset-md-3">
        {{ html()->submit($text)->class('btn btn-primary float-end')
            ->attributes($attributes->merge(['dusk' => 'submit'])->getAttributes()) }}
    </div>
</div>
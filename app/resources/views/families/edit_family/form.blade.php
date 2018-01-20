{{ Form::model($family, ['class' => 'form-horizontal', 'id' => 'edit-family-form']) }}
@include('forms.family', ['submit_text'=>'Opslaan'])
{{ Form::close() }}

<script>
    $(document).ready(function () {
        const form = $('#edit-family-form');
        const form_parent = form.parent();
        form.submit(function (event) {
            event.preventDefault();
            form_parent.load(
                '{!! route('submit_edit_family_form', ['family_id'=>$family->id]) !!}',
                form.serializeArray()
            );
            $(window).trigger('families:updated');
        });
    });
</script>
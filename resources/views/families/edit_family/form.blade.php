{{ Form::model($family, ['class' => 'form-horizontal', 'id' => 'edit-family-form']) }}
@include('families.forms.family')
{{ Form::close() }}

<script>
    $(document).ready(function () {
        const form = $('#edit-family-form');
        form.submit(function (event) {
            event.preventDefault();
            form.parent().load(
                '{!! route('submit_edit_family_form', ['family_id'=>$family->id]) !!}',
                form.serializeArray()
            );
        });
    });
</script>
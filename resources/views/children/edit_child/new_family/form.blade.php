<h4>Nieuwe voogd toevoegen</h4>
{{ Form::open(['class' => 'form-horizontal', 'id' => 'link-new-family']) }}
{{ Form::bsText('guardian_first_name') }}
{{ Form::bsText('guardian_last_name') }}
{{ Form::bsDropdown('tariff_id', $all_tariffs) }}
{{ Form::bsText('remarks') }}
{{ Form::bsText('contact') }}
{{ Form::bsSubmit() }}
{{ Form::close() }}

<script>
    $(function () {
        const form = $('#link-new-family');
        form.submit(function (event) {
            event.preventDefault();
            $('#link-new-child-family').load(
                '{!! route('submit_link_new_child_family_form', ['child_id' => $child->id]) !!}',
                form.serializeArray()
            )
        });
    });
</script>
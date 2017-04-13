<h3 id="edit-child-details-title">Details kind</h3>
{{ Form::model($child, ['class' => 'form-horizontal', 'id' => 'edit-child-form']) }}
{{ Form::bsText('first_name') }}
{{ Form::bsText('last_name') }}
{{ Form::bsNumber('birth_year') }}
{{ Form::bsDropdown('age_group', $all_age_groups) }}
{{ Form::bsText('remarks') }}
{{ Form::bsSubmit() }}
{{ Form::close() }}

<script>
    $(document).ready(function () {
        function reloadEditChildDetailsForm() {
            let container = $('#edit-child-families-form-title').parent();
            container.load(container.data('url'));
        }

        $('#edit-child-form').submit(function (event) {
            event.preventDefault();
            const form = $('#edit-child-form');
            form.parent().load(
                '{!! route('update_child_details', ['child_id'=>$child->id]) !!}',
                form.serializeArray()
            );
        });
    });
</script>
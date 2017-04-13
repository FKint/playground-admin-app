<h3>Details kind</h3>
{{ Form::model($child, ['class' => 'form-horizontal', 'id' => 'edit-child-form']) }}
@include('children.forms.child')
{{ Form::close() }}

<script>
    $(document).ready(function () {
        function reloadEditChildDetailsForm() {
            let container = $('#edit-child-div');
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
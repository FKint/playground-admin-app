<h3>Details kind</h3>
<div class="alert alert-success hidden" id="child-details-success-div">
    Wijzigingen opgeslagen.
</div>
<div class="alert alert-danger hidden" id="child-details-error-div">
    <ul id="child-details-error-list">
    </ul>
</div>
{{ Form::model($child, ['class' => 'form-horizontal', 'id' => 'edit-child-form']) }}
@include('forms.child')
{{ Form::close() }}

<script>
    $(function () {
        function reloadEditChildDetailsForm() {
            let container = $('#edit-child-div');
            container.load(container.data('url'));
        }

        const error_list = $('#child-details-error-list');
        const error_div = $('#child-details-error-div');
        const success_div = $('#child-details-success-div');
        const form = $('#edit-child-form');
        form.submit(function (event) {
            error_list.empty();
            error_div.addClass('hidden');
            success_div.addClass('hidden');
            event.preventDefault();
            const form = $('#edit-child-form');
            $.post(
                '{!! route('update_child_details', ['child_id'=>$child->id]) !!}',
                form.serialize()
            ).done(function (resp) {
                success_div.removeClass('hidden');
                $(window).trigger('children:updated');
            }).fail(function (resp) {
                for (const key in resp.responseJSON) {
                    const field_errors = resp.responseJSON[key];
                    for (const error_index in field_errors) {
                        const error_message = field_errors[error_index];
                        error_list.append($('<li>').text(error_message));
                    }
                }
                error_div.removeClass('hidden');
            });
        });
        form.on('change', 'form-control', function () {
            success_div.hide();
        });
    });
</script>
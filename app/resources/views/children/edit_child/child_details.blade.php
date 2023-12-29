<h3>Details kind</h3>
<div class="alert alert-success hidden" id="child-details-success-div">
    Wijzigingen opgeslagen.
</div>
<div class="alert alert-danger hidden" id="child-details-error-div">
    Failure: <span id="child-details-error-summary"></span>
    <ul id="child-details-error-list"></ul>
</div>
{{ html()->modelForm($child)->class('form-horizontal')->id('edit-child-form')->attributes(['dusk' => 'edit-child-form'])->open() }}
@include('forms.child')
{{ html()->closeModelForm() }}

<script>
    $(function () {
        const child_update_success = $('#child-details-success-div');
        const child_update_fail = $('#child-details-error-div');
        const child_details_error_summary = $('#child-details-error-summary');
        const child_details_error_list = $('#child-details-error-list');
        const form = $('#edit-child-form');
        form.submit(function (event) {
            child_details_error_list.empty();
            child_update_fail.addClass('hidden');
            child_update_success.addClass('hidden');
            event.preventDefault();
            const form = $('#edit-child-form');
            $.post(
                '{!! route('internal.update_child_details', ['child'=>$child]) !!}',
                form.serialize()
            ).done(function (resp) {
                child_update_success.removeClass('hidden');
                $(window).trigger('children:updated');
            }).fail(function (resp) {
                const response = resp.responseJSON;
                child_update_fail.removeClass('hidden');
                child_details_error_summary.text(response.message);
                Object.values(response.errors).forEach(field_errors => {
                    Object.values(field_errors).forEach(error_message => {
                        child_details_error_list.append($('<li>').text(error_message));
                    });
                });
            });
        });
        form.on('change', 'form-control', function () {
            success_div.hide();
        });
    });
</script>

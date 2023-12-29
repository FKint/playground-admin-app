<div class="alert alert-success hidden" id="family-details-success-div">
    Wijzigingen opgeslagen.
</div>
<div class="alert alert-danger hidden" id="family-details-error-div">
    Failure: <span id="family-details-error-summary"></span>
    <ul id="family-details-error-list">
    </ul>
</div>
{{ html()->modelForm($family)->class('form-horizontal')->id('edit-family-form')->attributes(['dusk' => 'edit-family-form'])->open() }}
<x-form-contents.family submit-text="Opslaan" />
{{ html()->closeModelForm() }}

<script>
    $(document).ready(function () {
        const form = $('#edit-family-form');
        const family_update_success = $('#family-details-success-div');
        const family_update_fail = $('#family-details-error-div');
        const family_details_error_summary = $('#family-details-error-summary');
        const family_details_error_list = $('#family-details-error-list');
        form.submit(function (event) {
            event.preventDefault();
            family_update_fail.addClass('hidden');
            family_update_success.addClass('hidden');
            family_details_error_summary.empty();
            family_details_error_list.empty();
            $.post(
                '{!! route('api.update_family', ['family'=>$family]) !!}',
                form.serialize()
            ).done(function (resp) {
                family_update_success.removeClass('hidden');
                $(window).trigger('families:updated');
            }).fail(function (resp) {
                const response = resp.responseJSON;
                family_update_fail.removeClass('hidden');
                family_details_error_summary.text(response.message);
                Object.values(response.errors).forEach(field_errors => {
                    Object.values(field_errors).forEach(error_message => {
                        family_details_error_list.append($('<li>').text(error_message));
                    });
                });
            });
        });
    });
</script>
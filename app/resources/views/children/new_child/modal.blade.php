@push('modals')
<div class="modal fade" tabindex="-1" role="dialog" id="new-child-modal" dusk="new-child-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Nieuw kind</h4>
            </div>
            <div class="modal-body" id="new-child-modal-body">
                <div class="alert alert-danger hidden" id="new-child-errors-div">
                    Failure: <span id="new-child-error-summary"></span>
                    <ul id="new-child-errors-list"></ul>
                </div>
                {{ html()->form()->class('form-horizontal')->id('new-child-form')->open() }}
                <x-form-contents.child />
                {{ html()->form()->close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel-new-child">Sluiten
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endpush

@push('scripts')
<script>
    function showNewChildModal(done_callback) {
        closeAllModals();
        $('#new-child-form')[0].reset();
        $('#new-child-modal').modal('show');
        if (done_callback) {
            $('#new-child-form').off('children:updated').on('children:updated', done_callback);
        }
    }
    $(function () {
        const new_child_fail = $('#new-child-errors-div');
        const new_child_errors_list = $('#new-child-errors-list');
        const new_child_error_summary = $('#new-child-error-summary');
        $('#new-child-form').submit(function () {
            new_child_fail.addClass('hidden');
            new_child_errors_list.empty();
            $.post('{{ route('api.create_new_child') }}',
                $(this).serialize()
            ).done(function (resp) {
                $('#new-child-form').trigger('children:updated');
                $(window).trigger('children:updated');
                showEditChildModal(resp.id, 'families');
            }).fail(function (resp) {
                const response = resp.responseJSON;
                new_child_error_summary.text(response.message);
                Object.values(response.errors).forEach(field_errors => {
                    Object.values(field_errors).forEach(error_message => {
                        new_child_errors_list.append($('<li>').text(error_message));
                    });
                });
                new_child_fail.removeClass('hidden');
            });
            return false;
        });
        const age_groups = {!! $year->age_groups !!};
        const new_child_form = $('#new-child-form');
        $('#new-child-modal').on('shown.bs.modal', function(e){
            new_child_form.find('input[name=first_name]').first().focus();
        });
        
        new_child_form.find('input[name=birth_year]').change(function () {
            new_child_form.find('select[name=age_group_id]').val(0);
            const birth_year = parseInt($(this).val());
            if (isNaN(birth_year))
                return true;

            function getYearFromDate(date_string) {
                return parseInt(date_string.substring(0, 4));
            }

            for (let i = 0; i < age_groups.length; ++i) {
                const start_year = getYearFromDate(age_groups[i].start_date);
                const end_year = getYearFromDate(age_groups[i].end_date);
                if (start_year <= birth_year && birth_year < end_year) {
                    new_child_form.find('select[name=age_group_id]').val(age_groups[i].id);
                    return;
                }
            }
        });
    });
</script>
@endpush
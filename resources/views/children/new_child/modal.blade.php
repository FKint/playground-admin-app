@push('modals')
<div class="modal fade" tabindex="-1" role="dialog" id="new-child-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Nieuw kind</h4>
            </div>
            <div class="modal-body" id="new-child-modal-body">
                <div class="alert alert-danger hidden" id="new-child-errors-div">
                    <ul id="new-child-errors-list">

                    </ul>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-child-form']) }}
                @include('forms.child')
                {{ Form::close() }}
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
    function showNewChildModal() {
        closeAllModals();
        $('#new-child-form')[0].reset();
        $('#new-child-modal').modal('show');
    }
    $(function () {
        $('#new-child-form').submit(function () {
            $('#new-child-errors-div').addClass('hidden');
            $('#new-child-errors-list').empty();
            $.post('{{ route('submitNewChild') }}',
                $(this).serialize()
            ).done(function (resp) {
                showEditChildModal(resp.id, 'families');
            }).fail(function (resp) {
                for (const key in resp.responseJSON) {
                    const field_errors = resp.responseJSON[key];
                    for (const error_key in field_errors) {
                        $('#new-child-errors-list').append($('<li>').text(field_errors[error_key]));
                    }
                }
                $('#new-child-errors-div').removeClass('hidden');
            });
            return false;
        });
        const age_groups = {!! $all_age_groups !!};
        const new_child_form = $('#new-child-form');
        new_child_form.find('input[name=first_name]').first().focus();
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
@push('modals')
<div class="modal fade" tabindex="-1" role="dialog" id="edit-child-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Kind wijzigen</h4>
            </div>
            <div class="modal-body" id="edit-child-modal-body">
                @include('children.edit_child.content')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel-edit-child">Sluiten
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endpush

@push('scripts')
<script>
    function showEditChildModal(child_id, start_tab) {
        closeAllModals();
        if(!start_tab){
            start_tab = "info";
        }
        $('#edit-child-tablist').find('li[data-tag="'+start_tab+'"]:first > a').click();
        $('#edit-child-modal').modal('show');
        const edit_child_form_url = '{!! route('edit_child_form') !!}' + '?child_id=' + child_id;
        $('#edit-child-info-div')
            .data('url', edit_child_form_url)
            .load(edit_child_form_url);

        const edit_child_families_form_url = '{!! route('edit_child_families_form') !!}' + '?child_id=' + child_id;
        $('#edit-child-families-div')
            .data('url', edit_child_families_form_url)
            .load(edit_child_families_form_url);
    }
</script>
@endpush
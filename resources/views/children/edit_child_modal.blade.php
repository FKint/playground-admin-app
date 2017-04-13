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
                <div id="edit-form-div"></div>
                <div id="edit-families-div"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-primary">Opslaan</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endpush

@push('scripts')
<script>
    // on .btn-edit-child click -> load modal for child with id stored in trigger element's data-child-id attribute.
    $('body').on('click', '.btn-edit-child', function () {
        // TODO: show spinning cog on modal while waiting for the form to load
        $('#edit-child-modal').modal('show');
        $('#edit-form-div')
            .load('{!! route('edit_child_form') !!}' + '?child_id=' + $(this).attr('data-child-id'));
        let edit_child_families_form_url = '{!! route('edit_child_families_form') !!}' + '?child_id=' + $(this).attr('data-child-id');
        $('#edit-families-div')
            .data('url', edit_child_families_form_url)
            .load(edit_child_families_form_url);
    });
</script>
@endpush
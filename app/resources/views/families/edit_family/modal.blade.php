@push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="edit-family-modal" dusk="edit-family-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Wijzig voogd</h4>
                </div>
                <div class="modal-body" id="edit-family-modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" dusk="btn-close-edit-family">Sluiten</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endpush

@push('scripts')
    <script>
        function showEditFamilyModal(family_id, update_callback) {
            closeAllModals();
            const form_parent = $('#edit-family-modal-body');
            form_parent.empty();
            // TODO: show spinning cog on modal while waiting for the form to load
            $('#edit-family-modal').modal('show');

            const edit_family_url = '{!! route('internal.load_edit_family_form', ['family' => 'FAMILY_ID']) !!}'
                .replace('FAMILY_ID', family_id);
            form_parent
                .data('url', edit_family_url)
                .load(edit_family_url);
        }
    </script>
@endpush
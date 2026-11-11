@push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="edit-family-modal" dusk="edit-family-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5">Wijzig voogd</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="edit-family-modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" dusk="btn-close-edit-family">Sluiten</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endpush

@push('scripts')
    <script type="module">
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
        window.showEditFamilyModal = showEditFamilyModal;
    </script>
@endpush
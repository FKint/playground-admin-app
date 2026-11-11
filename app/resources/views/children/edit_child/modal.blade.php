@push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="edit-child-modal" dusk="edit-child-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5">Kind wijzigen</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="edit-child-modal-body">
                    @include('children.edit_child.content')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-close-edit-child" dusk="btn-close-edit-child">
                        Sluiten
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endpush

@push('scripts')
    <script type="module">
        function showEditChildModal(child_id, start_tab) {
            closeAllModals();
            if (!start_tab) {
                start_tab = "info";
            }
            $('#edit-child-tablist').find('li[data-tag="' + start_tab + '"]:first > a')[0].click();
            $('#edit-child-modal').modal('show');
            const edit_child_form_url = '{!! route('internal.edit_child_form', ['child' => 'CHILD_ID']) !!}'
                .replace('CHILD_ID', child_id);
            $('#edit-child-info-div')
                .data('url', edit_child_form_url)
                .load(edit_child_form_url);

            const edit_child_families_form_url = '{!! route('internal.edit_child_families_form', ['child' => 'CHILD_ID']) !!}'
                .replace('CHILD_ID', child_id);
            $('#edit-child-families-div')
                .data('url', edit_child_families_form_url)
                .load(edit_child_families_form_url);
        }
        window.showEditChildModal = showEditChildModal;
    </script>
@endpush

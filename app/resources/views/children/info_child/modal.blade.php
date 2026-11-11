@push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="info-child-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5">Kind info</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="info-child-modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endpush

@push('scripts')
    <script type="module">
        function showChildInfoModal(child_id) {
            closeAllModals();
            $('#info-child-modal').modal('show');
            // TODO: show spinning cog on modal while waiting for the form to load
            const info_child_url = '{!! route('internal.info_child', ['child' => 'CHILD_ID']) !!}'
                .replace('CHILD_ID', child_id);
            $('#info-child-modal-body').load(info_child_url);
        }
        window.showChildInfoModal = showChildInfoModal;
    </script>
@endpush
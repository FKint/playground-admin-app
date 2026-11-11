@push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="family-children-modal" dusk="family-children-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5">Kinderen voor voogd</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="family-children-modal-body">

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
        function showFamilyChildrenModal(family_id) {
            closeAllModals();
            // TODO: show spinning cog on modal while waiting for the form to load
            $('#family-children-modal').modal('show');

            const family_children_url = '{!! route('internal.load_family_children', ['family' => 'FAMILY_ID']) !!}'
                .replace('FAMILY_ID', family_id);
            $('#family-children-modal-body')
                .data('url', family_children_url)
                .load(family_children_url);
        }
        window.showFamilyChildrenModal = showFamilyChildrenModal;

    </script>
@endpush
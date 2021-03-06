@push('modals')
    <div class="modal fade" tabindex="-1" role="dialog" id="family-children-modal" dusk="family-children-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Kinderen voor voogd</h4>
                </div>
                <div class="modal-body" id="family-children-modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endpush

@push('scripts')
    <script>
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

    </script>
@endpush
@push('modals')
<div class="modal fade" tabindex="-1" role="dialog" id="info-child-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Kind info</h4>
            </div>
            <div class="modal-body" id="info-child-modal-body">
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
    function showChildInfoModal(child_id) {
        closeAllModals();
        $('#info-child-modal').modal('show');
        // TODO: show spinning cog on modal while waiting for the form to load
        const info_child_url = '{!! route('info_child') !!}?child_id=' + child_id;
        $('#info-child-modal-body').load(info_child_url);
    }
</script>
@endpush
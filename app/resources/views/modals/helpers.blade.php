@push('scripts')
<script type="module">
    function closeAllModals() {
        $('.modal').modal('hide');
    }
    window.closeAllModals = closeAllModals;
</script>
@endpush
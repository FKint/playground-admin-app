<script>
    $(function () {
        function reloadEditChildFamiliesDiv() {
            const container = $('#edit-families-div');
            container.load(container.data('url'));
        }

        reloadEditChildFamiliesDiv();
    });
</script>
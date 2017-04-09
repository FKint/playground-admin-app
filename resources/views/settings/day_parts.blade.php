<table class="table table-bordered" id="day-parts-table">
    <thead>
    <tr>
        <th>Omschrijving</th>
        <th>Volgorde</th>
    </tr>
    </thead>
</table>


@push('scripts')
<script>
    $(function () {
        $('#day-parts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('getDayParts') !!}',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'order', name: 'order'}
            ],
        });
    });
</script>
@endpush

<table class="table table-bordered" id="supplements-table">
    <thead>
    <tr>
        <th>Naam</th>
        <th>Prijs</th>
    </tr>
    </thead>
</table>


@push('scripts')
<script>
    $(function () {
        $('#supplements-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('getSupplements') !!}',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'price', name: 'price'}
            ],
        });
    });
</script>
@endpush

<table class="table table-bordered" id="tariffs-table">
    <thead>
    <tr>
        <th>Omschrijving</th>
        <th>Afkorting</th>
        <th>Dagprijs eerste kind</th>
        <th>Dagprijs latere kinderen</th>
        <th>Weekprijs eerste kind</th>
        <th>Weekprijs latere kinderen</th>
    </tr>
    </thead>
</table>


@push('scripts')
<script>
    $(function () {
        $('#tariffs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('api.datatables.tariffs') !!}',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'abbreviation', name: 'abbreviation'},
                {data: 'day_first_child', name: 'day_first_child'},
                {data: 'day_later_children', name: 'day_later_children'},
                {data: 'week_first_child', name:'week_first_child'},
                {data: 'week_later_children', name: 'week_later_children'}
            ],
        });
    });
</script>
@endpush

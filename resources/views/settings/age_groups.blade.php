<table class="table table-bordered" id="age-groups-table">
    <thead>
    <tr>
        <th>Afkorting</th>
        <th>Naam</th>
        <th>Startjaar</th>
        <th>Eindjaar</th>
    </tr>
    </thead>
</table>


@push('scripts')
<script>
    $(function () {
        $('#age-groups-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getAgeGroups') !!}',
            columns: [
                {data: 'abbreviation', name: 'abbreviation'},
                {data: 'name', name: 'name'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'}
            ],
        });
    });
</script>
@endpush

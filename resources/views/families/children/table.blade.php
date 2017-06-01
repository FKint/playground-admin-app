<div class="row">
    <div class="col-xs-12">
        <table class="table table-bordered" id="family-children-table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Naam</th>
                <th>Geboortejaar</th>
                <th>Werking</th>
                <th>Belangrijk</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    $(function () {
        $('#family-children-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('getFamilyChildren', ['family_id' => $family->id]) !!}',
            columns: [
                {data: 'first_name', name: 'first_name'},
                {data: 'last_name', name: 'last_name'},
                {data: 'birth_year', name: 'birth_year'},
                {data: 'age_group_id', name: 'age_group_id'},
                {data: 'remarks', name: 'remarks'}
            ]
        });
    });
</script>
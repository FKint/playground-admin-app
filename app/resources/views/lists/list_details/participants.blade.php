<div class="row">
    <h2>Deelnemer toevoegen</h2>
    <form class="typeahead" role="search">
        <div class="form-group" dusk="child-family-search-typeahead">
            <input type="search" id="child-family-search" name="q" class="form-control" placeholder="Search"
                   autocomplete="off">
        </div>
    </form>
</div>
<div class="row">
    <h2>Huidige deelnemers</h2>
    <table class="table table-bordered" id="participants-table" dusk="participants-table">
        <thead>
        <tr>
            <th data-class-name="export">Kind</th>
            <th data-class-name="export">Voogd</th>
            <th>Verwijderen</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        $(function () {
            const table_element = $('#participants-table');
            const table = table_element.DataTable({
                processing: true,
                dom: 'Blfrtip',
                serverSide: false,
                ajax: '{!! route('api.datatables.list_participants', ['list'=>$list]) !!}',
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: '.export'
                        }
                    }
                ],
                orderCellsTop: true,
                columns: [
                    {
                        data: 'child',
                        name: 'child',
                        render: function (data, type, full) {
                            return data.full_name;
                        }
                    },
                    {
                        name: 'family',
                        data: 'family',
                        render: function (data, type, full) {
                            return data.id + ": " + data.guardian_full_name;
                        }
                    },
                    {
                        sortable: false,
                        searchable: false,
                        data: 'child_family_id',
                        render: function (data, type, full) {
                            return '<a href="#" class="btn-remove-child-family-list" data-child-family-id="' + data + '">Uitschrijven</a>';
                        }
                    }
                ]
            });
            table_element.on('click', '.btn-remove-child-family-list', function () {
                const child_family_id = $(this).data('child-family-id');
                $.post('{{ route('api.remove_participant_from_list', ['activity_list' => $list, 'child_family' => 'CHILD_FAMILY_ID']) }}'
                    .replace('CHILD_FAMILY_ID', child_family_id)
                ).done(function () {
                    table.ajax.reload();
                }).fail(function () {
                    alert("Failed!");
                });
            });
            const engine = new Bloodhound({
                remote: {
                    url: '{!! route('api.typeahead.child_family_suggestions_for_list', ['list' => $list]) !!}?q=%QUERY%',
                    wildcard: '%QUERY%',
                    cache: false
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                limit: 10
            });
            $('#child-family-search').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                source: engine.ttAdapter(),
                name: 'child-family-list',
                display: function (data) {
                    return data.child.full_name + " (Voogd " + data.family.id + ": " + data.family.guardian_full_name + ")";
                },
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    suggestion: function (data) {
                        const label = data.child.full_name + " (Voogd " + data.family.id + ": " + data.family.guardian_full_name + ")";
                        return '<a class="list-group-item" href="#">' + label + '</a>';
                    }
                }
            }).on('typeahead:selected', function (event, suggestion) {
                $.post('{!! route('api.add_participant_to_list', ['activity_list' => $list, 'child_family' => 'CHILD_FAMILY_ID']) !!}'
                    .replace('CHILD_FAMILY_ID', suggestion.id)
                ).done(function () {
                    $('#child-family-search').typeahead('val', '');
                    table.ajax.reload();
                }).fail(function () {
                    alert('Adding child family failed!');
                });
            }).focus();
        });
    </script>
@endpush
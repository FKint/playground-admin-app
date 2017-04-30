<table class="table table-bordered" id="registrations-table">
    <thead>
    <tr>
        <th>Voornaam</th>
        <th>Naam</th>
        <th>Werking</th>
        <th>Dagdeel</th>
        <th>Extraatjes</th>
        <th>Aanwezig</th>
        <th>Details - Kind</th>
        <th>Details - Familie</th>
        <th>Registratieformulier</th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-age-group">
                <option value="all">Alle</option>
                @foreach($all_age_groups as $age_group)
                    <option value="{{ $age_group->id }}">
                        {{ $age_group->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-daypart">
                <option value="all">Alle</option>
                @foreach($all_day_parts as $day_part)
                    <option value="{{ $day_part->id }}">
                        {{ $day_part->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-supplement">
                <option value="all">Alle</option>
                @foreach($all_supplements as $supplement)
                    <option value="{{ $supplement->id }}">
                        {{ $supplement->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-attended">
                <option value="all">Alle</option>
                <option value="1">Ja</option>
                <option value="0">Nee</option>
            </select>
        </td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </thead>
</table>

@push('scripts')
<script>
    $(function () {
        const table_element = $('#registrations-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getRegistrations', array('playground_day_id'=> $playground_day->id)) !!}',
            dom: 'Blfrtip',
            buttons: ['pdfHtml5'],
            orderCellsTop: true,
            columns: [
                {data: 'child.first_name', name: 'child.first_name'},
                {data: 'child.last_name', name: 'child.last_name'},
                {
                    data: {
                        '_': 'age_group.id',
                        'display': 'age_group.name'
                    },
                    name: 'age_group.id'
                },
                {
                    data: {
                        '_': 'day_part_id',
                        'display': function (data, type, full, meta) {
                            return data.day_part.name;
                        }
                    }
                },
                {
                    data: {
                        '_': function (data, type, full, meta) {
                            const mapped = _.map(data.supplements, function (d) {
                                return d.id;
                            });
                            return mapped.join();
                        },
                        'display': function (data, type, full, meta) {
                            const mapped = _.map(data.supplements, function (d) {
                                return d.name;
                            });
                            return mapped.join(", ");
                        }
                    }
                },
                {
                    data: {
                        '_': 'attended',
                        'display': function (data, type, full, meta) {
                            if (parseInt(data.attended)) {
                                return "Ja";
                            } else {
                                return "Nee";
                            }
                        }
                    },
                    name: 'attended'
                },
                {
                    searchable: false,
                    sortable: false,
                    name: 'child_details',
                    data: 'child_id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-show-child" href="#" data-child-id="' + data + '">Kind</a>';
                    }
                },
                {
                    searchable: false,
                    sortable: false,
                    name: 'family_details',
                    data: 'family_id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-show-family" href="#" data-family-id="' + data + '">Familie</a>';
                    }
                },
                {
                    searchable: false,
                    sortable: false,
                    name: 'family_week_registration',
                    data: 'family_id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-family-week-registration" href="{{ route('show_edit_registration', ['week_id'=>$playground_day->week->id]) }}?family_id=' + data + '" data-family-id="' + data + '">Registratie</a>';
                    }
                }
            ]
        });

        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                const selected_age_group_id = $('#select-age-group').val();
                if (selected_age_group_id === 'all') {
                    return true;
                }
                return (parseInt(data[2]) === parseInt(selected_age_group_id))
            }
        );
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                const selected_day_part = $('#select-daypart').val();
                if (selected_day_part === 'all') {
                    return true;
                }
                return parseInt(data[3]) === parseInt(selected_day_part);
            }
        );
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                const selected_supplement = $('#select-supplement').val();
                if (selected_supplement === 'all') {
                    return true;
                }
                return _.split(data[4], ',').includes(selected_supplement);
            }
        );
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                const selected_attended = $('#select-attended').val();
                if (selected_attended === 'all') {
                    return true;
                }
                return parseInt(data[5]) === parseInt(selected_attended);
            }
        );
        $('.registrations-table-filter').change(function () {
            table.draw();
        });
    });
</script>
@endpush
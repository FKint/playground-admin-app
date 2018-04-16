<table class="table table-bordered" id="registrations-table">
    <thead>
    <tr>
        <th data-class-name="export">Voornaam</th>
        <th data-class-name="export">Naam</th>
        <th data-class-name="export">Werking</th>
        <th data-class-name="export">Dagdeel</th>
        <th data-class-name="export">Extraatjes</th>
        <th data-class-name="export">Aanwezig</th>
        <th data-class-name="no-export">Details - Kind</th>
        <th data-class-name="no-export">Details - Familie</th>
        <th data-class-name="no-export">Registratieformulier</th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-age-group">
                <option value="all">Alle</option>
                @foreach($year->age_groups as $age_group)
                    <option value="{{ $age_group->id }}"
                            @if(isset($filter['age_group_id']) && $filter['age_group_id'] == $age_group->id) selected @endif>
                        {{ $age_group->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-daypart">
                <option value="all">Alle</option>
                @foreach($year->day_parts as $day_part)
                    <option value="{{ $day_part->id }}"
                            @if(isset($filter['day_part_id']) && $filter['day_part_id'] == $day_part->id) selected @endif>
                        {{ $day_part->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-supplement">
                <option value="all">Alle</option>
                @foreach($year->supplements as $supplement)
                    <option value="{{ $supplement->id }}"
                            @if(isset($filter['supplement_id']) && $filter['supplement_id'] == $supplement->id) selected @endif>
                        {{ $supplement->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control input-sm registrations-table-filter" id="select-attended">
                <option value="all">Alle</option>
                <option value="1" @if(isset($filter['present']) && $filter['present'] == "yes") selected @endif>Ja
                </option>
                <option value="0" @if(isset($filter['present']) && $filter['present'] == "no") selected @endif>Nee
                </option>
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
                ajax: '{!! route('api.datatables.registrations', ['playground_day'=> $playground_day]) !!}',
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        exportOptions: {
                            columns: ':visible.export'
                        },
                        title: "Registraties {{ $playground_day->date()->format('d-m-Y') }}",
                        customize: function (doc) {
                            doc.footer = function (page, pages) {
                                return {
                                    columns: [
                                        {
                                            alignment: 'center',
                                            text: [
                                                {text: page.toString(), italics: true},
                                                ' van ',
                                                {text: pages.toString(), italics: true}
                                            ]
                                        }
                                    ],
                                    margin: [10, 0]
                                };
                            };
                            let filter_text = "";
                            if ($('#select-age-group').val() !== 'all') {
                                filter_text += "Werking: " + $('#select-age-group option:selected').text().trim() + "\n";
                            }
                            if ($('#select-daypart').val() !== 'all') {
                                filter_text += "Dagdeel: " + $('#select-daypart option:selected').text().trim() + "\n";
                            }
                            if ($('#select-supplement').val() !== 'all') {
                                filter_text += "Extraatje: " + $('#select-supplement option:selected').text().trim() + "\n";
                            }
                            if ($('#select-attended').val() !== 'all') {
                                filter_text += "Aanwezig: " + $('#select-attended option:selected').text().trim() + "\n";
                            }
                            const search_query = $('#registrations-table').siblings('div.dataTables_filter').find('input').val().trim();
                            if (search_query.length > 0) {
                                filter_text += "Filter: " + search_query + "\n";
                            }
                            doc.content.splice(1, 0, {
                                text: filter_text,
                                alignment: "left"
                            });
                        }
                    }, 'colvis'
                ],
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
                            return '<a class="btn btn-xs btn-show-child-info" href="#" data-child-id="' + data + '">Kind</a>';
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
                            return ('<a class="btn btn-xs btn-family-week-registration" href="{{
                            route('internal.show_edit_registration', [
                                'week'=>$playground_day->week,
                                'family' => 'FAMILY_ID'
                            ]) }}" data-family-id="' + data + '">Registratie</a>')
                                .replace('FAMILY_ID', data);
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

            table_element.on('click', '.btn-show-child-info', function () {
                const child_id = $(this).attr('data-child-id');
                showChildInfoModal(child_id);
            });
        });
    </script>
@endpush
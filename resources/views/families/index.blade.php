@extends('layouts.app')
@include('modals.global')

@section('title')
    Voogden
@endsection
@push('styles')
<style>
    .table {
        table-layout: fixed;
        word-wrap: break-word;
    }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <a href="{!! route('show_new_family_with_children') !!}" class="btn btn-primary">Nieuwe voogd toevoegen</a>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <table class="table table-bordered" id="families-table">
            <thead>
            <tr>
                <th data-class-name="export">ID</th>
                <th data-class-name="export">Voornaam</th>
                <th data-class-name="export">Naam</th>
                <th data-class-name="export">Tarief</th>
                <th data-class-name="export">Belangrijk</th>
                <th data-class-name="export">Contact</th>
                <th data-class-name="export">Saldo</th>
                <th data-class-name="export">Kinderen detail</th>
                <th>Kinderen</th>
                <th>Wijzigen</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    // subtable: https://datatables.net/examples/api/row_details.html
    $(function () {
        const table_element = $('#families-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getFamilies') !!}',
            dom: 'Blfrtip',
            buttons: [{
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible.export'
                },
                title: "Gezinnen",
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
                                },
                                {
                                    alignment: 'right',
                                    text: "Datum: " + new Date().toDateString()
                                }
                            ],
                            margin: [10, 0]
                        };
                    };

                }
            }, 'colvis'],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'guardian_first_name', name: 'guardian_first_name'},
                {data: 'guardian_last_name', name: 'guardian_last_name'},
                {
                    data: {
                        '_': 'tariff.id',
                        'display': 'tariff.abbreviation'
                    },
                    name: 'tariff.id'
                },
                {data: 'remarks', name: 'remarks'},
                {data: 'contact', name: 'contact'},
                {
                    searchable: false,
                    name: 'saldo',
                    data: 'saldo',
                    render: function (data) {
                        return formatPrice(data);
                    }
                }, {
                    visible: false,
                    searchable: true,
                    name: 'children_details',
                    data: 'children_registrations',
                    render: function (data, type, full, meta) {
                        let res = "";
                        for (let i = 0; i < data.length; ++i) {
                            if (i > 0)
                                res += ', ';
                            res += data[i].full_child_name + ' (' + data[i].nb_registrations + ' dagen)';
                        }
                        return res;
                    }
                },
                {
                    searchable: false,
                    name: 'children',
                    data: 'id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-show-family-children" href="#" data-family-id="' + data + '">Kinderen</a>';
                    }
                },
                {
                    searchable: false,
                    name: 'edit',
                    data: 'id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-edit-family" href="#" data-family-id="' + data + '">Wijzig</a>';
                    }
                }
            ]
        });
        table_element.on('click', '.btn-show-family-children', function () {
            const family_id = $(this).data('family-id');
            showFamilyChildrenModal(family_id);
        });
        table_element.on('click', '.btn-edit-family', function () {
            const family_id = $(this).data('family-id');
            showEditFamilyModal(family_id);
        });
        $(window).on('families:updated', function () {
            table.ajax.reload();
        });
    });
</script>
@endpush

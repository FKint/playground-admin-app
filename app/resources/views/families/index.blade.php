@extends('layouts.internal')

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
        <a href="{!! route('internal.show_new_family_with_children') !!}" class="btn btn-primary">
            Nieuwe voogd toevoegen
        </a>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <table class="table table-bordered" id="families-table" dusk="families-table">
        <thead>
            <tr>
                <th data-class-name="export">ID</th>
                <th data-class-name="export">Voornaam</th>
                <th data-class-name="export">Naam</th>
                <th data-class-name="export">Tarief</th>
                <th data-class-name="export">Betaalwijze</th>
                <th data-class-name="export">Belangrijk</th>
                <th data-class-name="export">Contact</th>
                <th data-class-name="export">E-mail</th>
                <th data-class-name="export"
                    title="Het bedrag dat de vereniging tegoed heeft van deze voogd. Positief: de voogd moet nog extra betalen. Negatief: de voogd moet geld terug krijgen.">
                    Saldo
                </th>
                <th data-class-name="export"
                    title="De totale prijs van alle registraties voor deze voogd (inclusief de reeds betaalde).">
                    Totaal Verwacht
                </th>
                <th data-class-name="export">Kinderen detail</th>
                <th>Kinderen</th>
                <th>Wijzigen</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    {{-- Needs Invoice --}}
                    <select id="families-table-needs-invoice-filter" aria-controls="families-table"
                        class="form-control input-xs families-table-filter">
                        <option value="">Alles</option>
                        <option value="1">Factuur</option>
                        <option value="0">Cash</option>
                    </select>
                </td>
                <td></td>
                <td></td>
                <td>
                    {{-- Email --}}
                    <select id="families-table-email-filter" aria-controls="families-table"
                        class="form-control input-xs families-table-filter">
                        <option value="">Alles</option>
                        <option value="empty">Leeg</option>
                    </select>
                </td>
                <td>
                    {{-- Saldo --}}
                    <select id="families-table-saldo-filter" aria-controls="families-table"
                        class="form-control input-xs families-table-filter">
                        <option value="">Alles</option>
                        <option value="nonzero">Niet-nul</option>
                        <option value="zero">Nul</option>
                        <option value="negative">Negatief</option>
                        <option value="positive">Positief</option>
                    </select>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
            orderCellsTop: true,
            ajax: '{!! route('api.datatables.families') !!}',
            dom: 'Blfrtip',
            createdRow(row, data, dataIndex){
                $(row).attr('data-family-id', data.id);
            },
            buttons: [{
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: ':visible.export'
                },
                title: "Gezinnen",
                orientation: 'landscape',
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
            }, {
                extend: 'csv',   
                exportOptions: {
                    columns: ':visible.export'
                },
            }, 'colvis'],
            columns: [
                {data: 'id', name: 'id'},
                {
                    data: 'guardian_first_name', 
                    name: 'guardian_first_name', 
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'guardian_first_name');
                    },
                },
                {
                    data: 'guardian_last_name', 
                    name: 'guardian_last_name',
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'guardian_last_name');
                    },
                },
                {
                    data: {
                        '_': 'tariff.id',
                        'display': 'tariff.abbreviation'
                    },
                    name: 'tariff.id'
                },
                {
                    data(row, type, val, meta) {
                        return row.needs_invoice ? 'Factuur' : 'Cash';
                    },
                    name: 'needs_invoice'
                },
                {data: 'remarks', name: 'remarks', visible: false},
                {data: 'contact', name: 'contact'},
                {data: 'email', name: 'email', visible: false},
                {
                    searchable: false,
                    name: 'saldo',
                    data: 'saldo',
                    render: function (data) {
                        return formatPrice(data);
                    },
                    visible: false,
                }, {
                    searchable: false,
                    name: 'total_costs',
                    data: 'total_costs',
                    render: formatPrice,
                    visible: false,
                }, {
                    searchable: true,
                    name: 'children_details',
                    data: 'child_families',
                    render: function (child_families, type, full, meta) {
                        let records = [];
                        for(let i = 0; i < child_families.length; ++i){
                            records.push(child_families[i].child.full_name + " ("+child_families[i].nb_registrations + " dagen)");
                        }
                        return records.join(", ");
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
        function filterNeedsInvoice(data){
            const needs_invoice_filter = $('#families-table-needs-invoice-filter').val();
            if(needs_invoice_filter !== ''){
                return parseInt(needs_invoice_filter) === parseInt(data.needs_invoice);
            }
            return true;
        };
        function filterEmail(data){
            const email_filter = $('#families-table-email-filter').val();
            switch(email_filter){
                case 'empty':
                    return data.email == '';
            }
            return true;
        };
        function filterSaldo(data){
            const saldo_filter = $('#families-table-saldo-filter').val();
            const saldo = parseFloat(data.saldo);
            const eps = 0.005;
            switch(saldo_filter){
                case 'nonzero':
                    return Math.abs(saldo - 0.0) > eps;
                case 'zero':
                    return Math.abs(saldo - 0.0) < eps;
                case 'negative':
                    return saldo < -eps;
                case 'positive':
                    return saldo > eps;
                default:
                    return true;
            }
        };
        $.fn.dataTable.ext.search.push(
            function(settings, searchData, index, rowData, counter){
                if(settings.nTable != table_element.get(0)){
                    return true;
                }
                return filterNeedsInvoice(rowData) && filterEmail(rowData) && filterSaldo(rowData);
            }
        );
        $('.families-table-filter').change(function(){ table.draw(); });
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
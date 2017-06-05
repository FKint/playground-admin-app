@extends('layouts.app')

@section('content')
    <div class="row">
        <a class="btn btn-primary" href="{{ route('show_create_new_list') }}">Nieuwe lijst aanmaken</a>
    </div>
    <div class="row">
        <table class="table table-bordered table-striped" id="lists-table">
            <thead>
            <tr>
                <th data-class-name="export">ID</th>
                <th data-class-name="export">Naam</th>
                <th data-class-name="export">Datum</th>
                <th data-class-name="export">Prijs</th>
                <th data-class-name="export">Zichtbaar bij registratie</th>
                <th data-class-name="export">Zichtbaar op dashboard</th>
                <th>Details</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection
@push('scripts')

<script>
    $(function () {
        const table_element = $('#lists-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getLists') !!}',
            dom: 'Blfrtip',
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
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'date', name: 'date'},
                {
                    data: 'price',
                    render: function (data) {
                        return formatPrice(data);
                    }
                }, {
                    data: 'show_on_attendance_form',
                    name: 'show_on_attendance_form'
                }, {
                    data: 'show_on_dashboard',
                    name: 'show_on_dashboard'
                },
                {
                    searchable: false,
                    name: 'details',
                    data: 'id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-list-details" data-list-id="' + data + '">Details</a>';
                    }
                }
            ]
        });
        table_element.on('click', '.btn-list-details', function () {
            const list_id = $(this).data('list-id');
            window.location.href = '{!! route('show_list', ['list_id' => 'LISTID']) !!}'.replace('LISTID', list_id);
        });
    });
</script>
@endpush

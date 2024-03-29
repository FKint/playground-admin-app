@if(!$year->getActiveAdminSession())
    <div class="alert alert-danger">Geen kassa actief!</div>
@endif

<div class="row">
    <a href="{{ route('internal.close_admin_session') }}" class="btn btn-primary">Huidige kassa afsluiten</a>
    <a href="{{ route('internal.show_transactions_for_date') }}" class="btn btn-primary">Transacties vandaag</a>
</div>
<div class="row"></div>
<div class="row">
    <table class="table table-bordered" id="admin-sessions-table" dusk="admin-sessions-table">
        <thead>
        <tr>
            <th>Begin</th>
            <th>Einde</th>
            <th>Aantal transacties</th>
            <th>Verwachte inkomsten</th>
            <th>Getelde inkomsten</th>
            <th>Fout</th>
            <th>Verantwoordelijke</th>
            <th>Opmerkingen</th>
            <th>Wijzigen</th>
        </tr>
        </thead>
    </table>
</div>

@push('scripts')

<script>
    $(function () {
        $('#admin-sessions-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('api.datatables.admin_sessions') !!}',
            createdRow(row, data, dataIndex){
                $(row).attr('data-admin-session-id', data.id);
            },
            columns: [
                {
                    data: 'session_start',
                    name: 'session_start',
                    render: function (data) {
                        if (data === null) {
                            return 'actief';
                        }
                        return data;
                    }
                },
                {
                    data: 'session_end',
                    name: 'session_end',
                    render: function (data) {
                        if (data === null) {
                            return 'actief';
                        }
                        return data;
                    }
                },
                {
                    data: 'nb_transactions', 
                    name: 'nb_transactions',
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'nb_transactions');
                    },
                },
                {
                    data: 'expected_cash',
                    name: 'expected_cash',
                    render: function (data) {
                        return formatPrice(data);
                    },
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'expected_income');
                    },
                },
                {
                    data: 'counted_cash',
                    name: 'counted_cash',
                    render: function (data) {
                        return formatPrice(data);
                    },
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'actual_income');
                    },
                },
                {
                    data: 'error',
                    name: 'error',
                    render: function (data) {
                        return formatPrice(data);
                    },
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'error');
                    },
                },
                {
                    data: 'responsible_name',
                    name: 'responsible_name',
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'responsible_name');
                    },
                },
                {
                    data: 'remarks', 
                    name: 'remarks',
                    createdCell(td, cellData, rowData, row, col){
                        $(td).attr('data-field', 'remarks');
                    },
                },
                {
                    data: 'id',
                    name: 'id',
                    render: function (data, type, full) {
                        if(!full.finished){
                            return "";
                        }
                        return '<a href="{{ route('internal.show_edit_admin_session', ['admin_session' => 'SESSION_ID']) }}">Wijzigen</a>'.replace('SESSION_ID', data);
                    },
                }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
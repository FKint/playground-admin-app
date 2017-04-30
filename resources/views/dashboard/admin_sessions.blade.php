<h2>Kassa</h2>

@if(!$active_admin_session)
    <div class="alert alert-danger">Geen kassa actief!</div>
@endif

<div class="row">
    <a href="{{ route('close_admin_session') }}" class="btn btn-primary">Huidige kassa afsluiten.</a>
</div>
<div class="row"></div>
<div class="row">
    <table class="table table-bordered" id="admin-sessions-table">
        <thead>
        <tr>
            <th>Begin</th>
            <th>Einde</th>
            <th>Aantal transacties</th>
            <th>Verwachte inkomsten</th>
            <th>Getelde inkomsten</th>
            <th>Fout</th>
            <th>Opmerkingen</th>
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
            ajax: '{!! route('getAdminSessions') !!}',
            columns: [
                {data: 'session_start', name: 'session_start'},
                {data: 'session_end', name: 'session_end'},
                {data: 'nb_transactions', name: 'nb_transactions'},
                {data: 'expected_cash', name: 'expected_cash'},
                {data: 'counted_cash', name: 'counted_cash'},
                {data: 'error', name: 'error'},
                {data: 'remarks', name: 'remarks'}
            ],
        });
    });
</script>
@endpush
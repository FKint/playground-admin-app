@extends('layouts.internal')

@section('title')
    Transacties voor {{ $date->toDateString() }}
@endsection
@section('content')

<h1>Transacties voor {{ $date->toDateString() }}</h1>
<div class="row">
    <table class="table table-bordered" id="transactions-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tijdstip</th>
            <th>Voogd ID</th>
            <th>Voogd</th>
            <th>Verwacht</th>
            <th>Betaald</th>
            <th>Verschil</th>
            <th>Kassa&shy;verantwoordelijke</th>
            <th>Opmerkingen</th>
        </tr>
        </thead>
    </table>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        const table_element = $('#transactions-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('api.datatables.transactions_for_date', ['date'=>$date]) !!}',
            buttons: ['pdfHtml5'],
            order: [[1, "desc"]],
            columns: [
                {data: 'id', name: 'id'},
                {
                    data: 'created_at', 
                    name: 'created_at', 
                    render: function(data, type, full, meta){
                        const date = new Date(Date.parse(full.created_at));
                        return date.toLocaleDateString('nl-BE') + ' ' + date.toLocaleTimeString('nl-BE');
                    }
                },
                {data: 'family_id', name: 'family_id'},
                {data: 'family_name', name: 'family_name', data: 'family.guardian_full_name'},
                {
                    data: 'amount_expected',
                    name: 'amount_expected',
                    render: function (data) {
                        return formatPrice(data);
                    }
                },
                {
                    data: 'amount_paid',
                    name: 'amount_paid',
                    render: function (data) {
                        return formatPrice(data);
                    }
                },
                {
                    name: 'difference',
                    data: 'id',
                    render: function (data, type, full, meta) {
                        return formatPrice(full.amount_expected - full.amount_paid);
                    }
                },
                {
                    data: 'admin_session',
                    name: 'admin_session',
                    render: function (data, type, full, meta) {
                        if(full.admin_session.responsible_name){
                            return full.admin_session.responsible_name;
                        }
                        return '<i>Actieve Kassa</i>';
                    }
                },
                {data: 'remarks', name: 'remarks'}
            ]
        });
    });

</script>
@endpush
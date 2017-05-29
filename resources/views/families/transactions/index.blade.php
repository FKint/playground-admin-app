@extends('layouts.app')

@section('content')

    <div class="row">
        <table class="table table-bordered" id="transactions-table">
            <thead>
            <tr>
                <th>Datum</th>
                <th>Betaald</th>
                <th>Verwacht</th>
                <th>Verschil</th>
                <th>Verantwoordelijke</th>
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
            ajax: '{!! route('getFamilyTransactions', ['family_id'=>$family->id]) !!}',
            buttons: ['pdfHtml5'],
            columns: [
                {data: 'created_at', name: 'created_at'},
                {
                    data: 'amount_paid',
                    name: 'amount_paid',
                    render: function (data) {
                        return formatPrice(data);
                    }
                },
                {
                    data: 'amount_expected',
                    name: 'amount_expected',
                    render: function (data) {
                        return formatPrice(data);
                    }
                },
                {
                    name: 'amount_paid',
                    data: 'id',
                    render: function (data, type, full, meta) {
                        return formatPrice(full.amount_paid - full.amount_expected);
                    }
                },
                {
                    data: 'admin_session',
                    name: 'admin_session',
                    render: function (data, type, full, meta) {
                        return data.responsible_name;
                    }
                },
                {data: 'remarks', name: 'remarks'}
            ]
        });
    });

</script>
@endpush
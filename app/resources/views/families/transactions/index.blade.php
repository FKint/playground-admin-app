@extends('layouts.internal')

@section('title')
    Transactiegeschiedenis voor voogd {{ $family->id }} ({{ $family->guardian_full_name() }})
@endsection
@section('content')
    <h1>Transactiegeschiedenis voor voogd {{ $family->id }} ({{ $family->guardian_full_name() }})</h1>
    <div class="row">
        <div class="col-xs-12">
            Saldo: <b><span id="family-saldo">{{ $family->getCurrentSaldo() }}</span></b>
        </div>
    </div>
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
            ajax: '{!! route('api.datatables.family_transactions', ['family'=>$family]) !!}',
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
                    name: 'difference',
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
        $('#family-saldo').html(formatPrice($('#family-saldo').text()));
    });

</script>
@endpush
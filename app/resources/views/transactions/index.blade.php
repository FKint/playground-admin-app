@extends('layouts.internal')

@section('title')
Transacties voor {{ $date->format('d-m-Y') }}
@endsection
@section('content')

<h1>Transacties voor {{ $date->format('d-m-Y') }}</h1>
<div class="row">
    <div class="col-xs-1 col-lg-1">
        <button class="btn btn-default pull-right">
            <span class="glyphicon glyphicon-backward" id="btn-prev-day"></span>
        </button>
    </div>
    <div class="col-xs-3 col-lg-3">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy/mm/dd"
            id="transactions-datepicker" dusk="transactions-datepicker">
            <input type="text" class="form-control">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="col-xs-1 col-lg-1">
        <button class="btn btn-default pull-left" id="btn-next-day">
            <span class="glyphicon glyphicon-forward"></span>
        </button>
    </div>
</div>
<div class="row">
    <table class="table table-bordered" id="transactions-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tijdstip</th>
                <th>Voogd</th>
                <th>Verwacht</th>
                <th>Betaald</th>
                <th>Verschil</th>
                <th>Kassa&shy;verantwoordelijke</th>
                <th>Opmerkingen</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>
                    <!-- Voogd ID -->
                    <input class="form-control input-xs transactions-table-filter"
                        id="transactions-table-family-id-filter" aria-controls="transactions-table"
                        placeholder="Voogd ID filter" />
                </th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                    {{-- Remarks --}}
                    <select id="transactions-table-remarks-filter" aria-controls="transactions-table"
                        class="form-control input-xs transactions-table-filter">
                        <option value="0">Alles</option>
                        <option value="1">Met Opmerking</option>
                    </select>
                </th>
            </tr>
        </thead>
    </table>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        function goToMoment(m) {
            window.location.href = "{{ route('internal.show_transactions_for_date', ['date' => 'DATE']) }}".replace('DATE', m.format('YYYY-MM-DD'));
        }

        const default_date = new Date({{ $date->format('Y') }}, {{ $date->format('m') }} - 1, {{ $date->format('d') }});
        $('#transactions-datepicker').datepicker()
            .datepicker('update', default_date)
            .on('changeDate', function (event) {
                console.log(event.date);
                goToMoment(moment(event.date));
            });
        $('#btn-prev-day').click(function () {
            goToMoment(moment(default_date).subtract(1, 'days'));
        });
        $('#btn-next-day').click(function () {
            goToMoment(moment(default_date).add(1, 'days'));
        });
    });

    $(function () {
        const table_element = $('#transactions-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('api.datatables.transactions_for_date', ['date'=>$date->toDateString()]) !!}',
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
                {
                    data: 'family',
                    name: 'family',
                    searchable: true,
                    render(data, type, row, meta){
                        const route = "{{ route('internal.show_edit_registration', ['family' => 'FAMILY', 'week' => $playground_day->week_id]) }}".replace('FAMILY', data.id);
                        return '<a href="'+route+'">'+data.id+': ' + data.guardian_full_name + '</a>';
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
                {data: 'remarks', name: 'remarks', searchable: true}
            ]
        });
        function filterFamilyId(data){
            const family_id_filter = $('#transactions-table-family-id-filter').val();
            if(family_id_filter !== ''){
                return parseInt(family_id_filter) === parseInt(data.family_id);
            }
            return true;
        };
        function filterRemarks(data){
            const remarks_filter = $('#transactions-table-remarks-filter').val();
            if(parseInt(remarks_filter)>0){
                return data.remarks.length > 0;
            }
            return true;
        };
        $.fn.dataTable.ext.search.push(
            function(settings, searchData, index, rowData, counter){
                if(settings.nTable != table_element.get(0)){
                    return true;
                }
                return filterFamilyId(rowData) && filterRemarks(rowData);
            }
        );
        $('.transactions-table-filter').change(function(){ table.draw(); });
    });

</script>
@endpush
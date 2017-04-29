@extends('layouts.app')

@section('content')
    <h1>Registraties</h1>

    <div class="row">
        <a href="{{ route('show_find_family_registration', ['week_id' => $playground_day->week->id]) }}"
           class="btn btn-primary">Registreer betalingen/aanwezigheid</a>
    </div>

    <div class="row">&nbsp;</div>
    <div class="row">
        <table class="table table-bordered" id="registrations-table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Naam</th>
                <th>Werking</th>
                <th>Aanwezig</th>
                <th>Details - Kind</th>
                <th>Details - Familie</th>
                <th>Registratieformulier</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        const table_element = $('#registrations-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('getRegistrations', array('playground_day_id'=> $playground_day->id)) !!}',
            columns: [
                {data: 'child.first_name', name: 'child.first_name'},
                {data: 'child.last_name', name: 'child.last_name'},
                {data: 'age_group.name', name: 'age_group.name'},
                {data: 'attended', name: 'attended'},
                {
                    searchable: false,
                    sortable: false,
                    name: 'child_details',
                    data: 'child_id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-show-child" href="#" data-child-id="' + data + '">Kind</a>';
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
                        return '<a class="btn btn-xs btn-family-week-registration" href="#" data-family-id="' + data + '">Registratie</a>';
                    }
                }
            ]
        });
    });

</script>
@endpush

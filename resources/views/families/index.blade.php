@extends('layouts.app')


@include('families.children.modal')
@include('families.edit_family.modal')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <a href="{!! route('show_new_family_with_children') !!}" class="btn btn-primary">Nieuw gezin toevoegen</a>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <table class="table table-bordered" id="families-table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Naam</th>
                <th>Tarief</th>
                <th>Belangrijk</th>
                <th>Contact</th>
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
            buttons: [ 'pdfHtml5' ],
            columns: [
                {data: 'guardian_first_name', name: 'guardian_first_name'},
                {data: 'guardian_last_name', name: 'guardian_last_name'},
                {data: 'tariff_id', name: 'tariff_id'},
                {data: 'remarks', name: 'remarks'},
                {data: 'contact', name: 'contact'},
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
    });

</script>
@endpush

@extends('layouts.app')
@include('modals.global')

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
            <a href="{!! route('show_new_family_with_children') !!}" class="btn btn-primary">Nieuwe voogd toevoegen</a>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <table class="table table-bordered" id="families-table">
            <thead>
            <tr>
                <th>ID</th>
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
            buttons: ['pdfHtml5'],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'guardian_first_name', name: 'guardian_first_name'},
                {data: 'guardian_last_name', name: 'guardian_last_name'},
                {
                    data: {
                        '_': 'tariff.id',
                        'display': 'tariff.abbreviation'
                    },
                    name: 'tariff.id'
                },
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
        table_element.on('click', '.btn-show-family-children', function () {
            const family_id = $(this).data('family-id');
            showFamilyChildrenModal(family_id);
        });
        table_element.on('click', '.btn-edit-family', function () {
            const family_id = $(this).data('family-id');
            showEditFamilyModal(family_id);
        });
        $(window).on('families:updated', function(){
            table.ajax.reload();
        });
    });
</script>
@endpush

@extends('layouts.app')
@include('modals.global')
@include('children.new_child.modal')
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
            <a href="#" class="btn btn-primary" id="btn-new-child">Nieuw kind toevoegen</a>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <table class="table table-bordered" id="children-table">
            <thead>
            <tr>
                <th data-class-name="export">Voornaam</th>
                <th data-class-name="export">Naam</th>
                <th data-class-name="export">Geboortejaar</th>
                <th data-class-name="export">Werking</th>
                <th data-class-name="export">Belangrijk</th>
                <th>Info</th>
                <th>Wijzigen</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <select class="form-control input-sm children-table-filter" id="select-remarks">
                        <option value="all">Alle</option>
                        <option value="yes">Met opmerking</option>
                    </select>
                </td>
                <td></td>
                <td></td>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        const table_element = $('#children-table');
        const table = table_element.DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getChildren') !!}',
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
                {data: 'first_name', name: 'first_name'},
                {data: 'last_name', name: 'last_name'},
                {data: 'birth_year', name: 'birth_year'},
                {data: 'age_group.name', name: 'age_group.name', sortable: false},
                {data: 'remarks', name: 'remarks'},
                {
                    data: 'id',
                    render: function (data) {
                        return '<a class="btn btn-xs btn-show-child-info" data-child-id="' + data + '">Info</a>';
                    }
                },
                {
                    searchable: false,
                    name: 'edit',
                    data: 'id',
                    render: function (data, type, full, meta) {
                        return '<a class="btn btn-xs btn-edit-child" data-child-id="' + data + '">Wijzig</a>';
                    }
                }
            ]
        });
        $.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                const selected_remarks_value = $('#select-remarks').val();
                if (selected_remarks_value === 'all') {
                    return true;
                }
                if (selected_remarks_value === 'yes') {
                    return (data[4] && data[4].length > 0);
                }
                return true;
            }
        );
        $('.children-table-filter').change(function () {
            table.draw();
        });
        $('#btn-new-child').focus();


        table_element.on('click', '.btn-show-child-info', function () {
            const child_id = $(this).attr('data-child-id');
            showChildInfoModal(child_id);
        });
        table_element.on('click', '.btn-edit-child', function () {
            const child_id = $(this).attr('data-child-id');
            showEditChildModal(child_id);
        });

        $('#edit-child-modal').on('hidden.bs.modal', function () {
            $('#btn-new-child').focus();
        });
        $('#btn-new-child').click(function(){
            showNewChildModal();
        });
    });
</script>
@endpush

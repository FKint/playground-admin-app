@extends('layouts.app')

@include('children.edit_child.modal')
@include('children.info_child.modal')
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
            <a href="{!! route('show_new_child') !!}" class="btn btn-primary" id="btn-new-child">Nieuw kind toevoegen</a>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <table class="table table-bordered" id="children-table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Naam</th>
                <th>Geboortejaar</th>
                <th>Werking</th>
                <th>Belangrijk</th>
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
        const table = $('#children-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getChildren') !!}',
            dom: 'Blfrtip',
            buttons: ['pdfHtml5'],
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
                if(selected_remarks_value === 'yes'){
                    return (data[4] && data[4].length > 0);
                }
                return true;
            }
        );
        $('.children-table-filter').change(function () {
            table.draw();
        });
        $('#btn-new-child').focus();
    });

</script>
@endpush
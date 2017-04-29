@extends('layouts.app')

@include('children.edit_child.modal')

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <a href="{!! route('show_new_child') !!}" class="btn btn-primary">Nieuw kind toevoegen</a>
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
                <th>Wijzigen</th>
            </tr>
            <tr>
                <td>Voornaam filter</td>
                <td>Naam filter</td>
                <td>Geboortejaar filter</td>
                <td>Werking filter</td>
                <td>Belangrijk filter</td>
                <td>Wijzigen filter</td>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#children-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{!! route('getChildren') !!}',
            dom: 'Blfrtip',
            buttons: [ 'pdfHtml5' ],
            orderCellsTop: true,
            columns: [
                {data: 'first_name', name: 'first_name'},
                {data: 'last_name', name: 'last_name'},
                {data: 'birth_year', name: 'birth_year'},
                {data: 'age_group.name', name: 'age_group.name', sortable: false},
                {data: 'remarks', name: 'remarks'},
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
    });
</script>
@endpush
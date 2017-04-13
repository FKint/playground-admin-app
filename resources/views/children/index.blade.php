@extends('layouts.app')

@include('children.edit_child_modal')

@section('content')
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
        </thead>
    </table>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#children-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('getChildren') !!}',
            columns: [
                {data: 'first_name', name: 'first_name'},
                {data: 'last_name', name: 'last_name'},
                {data: 'birth_year', name: 'birth_year'},
                {data: 'age_group_id', name: 'age_group_id'},
                {data: 'remarks', name: 'remarks'},
                {
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
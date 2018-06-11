<table class="table table-bordered">
    <tr>
        <th>Datum</th>
        <th>Naam</th>
        <th>Inschrijvingen</th>
    </tr>
    @foreach($year->getDashboardLists() as $activity_list)
        <tr>
            <td>{{ $activity_list->date}}</td>
            <td>
                <a href="{{ route('internal.show_list', ['list'=>$activity_list]) }}">
                    {{ $activity_list->name }}
                </a>
            </td>
            <td>{{ $activity_list->child_families()->count() }}</td>
        </tr>
    @endforeach
</table>

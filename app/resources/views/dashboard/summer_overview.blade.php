<table class="table table-bordered">
    <tr>
        <th rowspan="2"></th>
        <th colspan="{{ $year->age_groups()->count() }}">Werking</th>
        <th rowspan="2">Totaal</th>
    </tr>
    <tr>
        @foreach($year->age_groups as $age_group)
            <th>{{ $age_group->abbreviation }}</th>
        @endforeach
    </tr>
    <tr>
        <th>Totaal</th>
        @foreach($year->age_groups as $age_group)
            <td>
                {{  $year->count_registrations_for_age_group($age_group) }}
            </td>
        @endforeach
        <td>
            {{ $year->count_registrations() }}
        </td>
    </tr>
    @foreach($year->playground_days as $playground_day)
        <tr>
            <th>
                {{ $playground_day->date()->format('Y-m-d') }}
            </th>
            @foreach($year->age_groups as $age_group)
                <td>
                    <a href="{{ route('internal.registrations_for_date', ['date' => $playground_day->date()->format('Y-m-d')]) }}?filter_age_group_id={{$age_group->id}}">
                        {{ $playground_day->count_registrations_for_age_group($age_group) }}
                    </a>
                </td>
            @endforeach
            <td>
                <a href="{{ route('internal.registrations_for_date', ['date' => $playground_day->date()->format('Y-m-d')]) }}">
                    {{ $playground_day->count_registrations() }}
                </a>
            </td>
        </tr>
    @endforeach
</table>
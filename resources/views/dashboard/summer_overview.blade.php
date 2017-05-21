<table class="table table-bordered">
    <tr>
        <th rowspan="2"></th>
        <th colspan="{{ count($all_age_groups) }}">Werking</th>
        <th rowspan="2">Totaal</th>
    </tr>
    <tr>
        @foreach($all_age_groups as $age_group)
            <th>{{ $age_group->abbreviation }}</th>
        @endforeach
    </tr>
    <tr>
        <th>Totaal</th>
        @foreach($all_age_groups as $age_group)
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
            @foreach($all_age_groups as $age_group)
                <td>
                    {{ $playground_day->count_registrations_for_age_group($age_group) }}
                </td>
            @endforeach
            <td>
                {{ $playground_day->count_registrations() }}
            </td>
        </tr>
    @endforeach
</table>
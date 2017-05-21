@if(!empty($today_playground_day))
    <table class="table table-bordered">
        <tr>
            <th rowspan="2"></th>
            <th colspan="{{ count($all_age_groups) }}" class="text-center">Werking</th>
            <th rowspan="2">Totaal</th>
        </tr>
        <tr>
            @foreach($all_age_groups as $age_group)
                <th>{{ $age_group->abbreviation }}</th>
            @endforeach
        </tr>
        @foreach($supplements as $supplement)
            <tr>
                <th>{{ $supplement->name }}</th>
                @foreach($all_age_groups as $age_group)
                    <td>
                        {{ $today_playground_day->count_supplements_for_age_group($supplement, $age_group) }}
                    </td>
                @endforeach
                <td>
                    {{ $today_playground_day->count_supplements($supplement) }}
                </td>
            </tr>
        @endforeach
        <th>Aanwezige kinderen</th>
        @foreach($all_age_groups as $age_group)
            <td>
                {{ $today_playground_day->count_registrations_for_age_group($age_group) }}
            </td>
        @endforeach
        <td>
            {{ $today_playground_day->count_registrations() }}
        </td>
    </table>
@else
    <div class="alert alert-warning">Geen werking vandaag!</div>
@endif

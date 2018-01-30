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
                        <a href="{{ route('internal.registrations_for_date', ['date' => $today_playground_day->date()->format('Y-m-d')]) }}?filter_supplement_id={{$supplement->id}}&filter_age_group_id={{$age_group->id}}">
                            {{ $today_playground_day->count_supplements_for_age_group($supplement, $age_group) }}
                        </a>
                    </td>
                @endforeach
                <td>
                    <a href="{{ route('internal.registrations_for_date', ['date' => $today_playground_day->date()->format('Y-m-d')]) }}?filter_supplement_id={{$supplement->id}}">
                        {{ $today_playground_day->count_supplements($supplement) }}
                    </a>
                </td>
            </tr>
        @endforeach
        <th>Aanwezige kinderen</th>
        @foreach($all_age_groups as $age_group)
            <td>
                <a href="{{ route('internal.registrations_for_date', ['date' => $today_playground_day->date()->format('Y-m-d')]) }}?filter_age_group_id={{$age_group->id}}">
                    {{ $today_playground_day->count_registrations_for_age_group($age_group) }}
                </a>
            </td>
        @endforeach
        <td>
            <a href="{{ route('internal.registrations_for_date', ['date' => $today_playground_day->date()->format('Y-m-d')]) }}">
                {{ $today_playground_day->count_registrations() }}
            </a>
        </td>
    </table>
@else
    <div class="alert alert-warning">Geen werking vandaag!</div>
@endif

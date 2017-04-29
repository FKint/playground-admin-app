@extends('layouts.app')

@section('content')
    <h1>Zoek familie</h1>
    <ul>
        @foreach($all_weeks as $w)
            <li>
                <a href="{!! route('show_find_family_registration', ['week_id'=>$w->id]) !!}">
                    {{ $w->first_day_of_week }}
                </a>
            </li>
        @endforeach
    </ul>

    <form class="typeahead" role="search">
        <div class="form-group">
            <input type="search" id="family-search" name="q" class="form-control" placeholder="Search"
                   autocomplete="off">
        </div>
    </form>

    @push('scripts')
    <script>
        $(function () {
            let engine = new Bloodhound({
                remote: {
                    url: '{!! route('getFamilySuggestions') !!}?q=%QUERY%',
                    wildcard: '%QUERY%'
                },
                datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
                queryTokenizer: Bloodhound.tokenizers.whitespace
            });
            $('#family-search').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            }, {
                source: engine.ttAdapter(),
                name: 'family-list',
                display: function (data) {
                    return data.id + ': ' + data.guardian_first_name + ' ' + data.guardian_last_name;
                },
                templates: {
                    empty: [
                        '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                    ],
                    suggestion: function (data) {
                        console.log(data);
                        return '<a href="{!! route('show_edit_registration',
                        ['week_id' => $week->id]) !!}?family_id=' + data.id + '" class="list-group-item">' + data.guardian_first_name + ' '
                            + data.guardian_last_name + '</a>';
                    }
                }
            });
        });
    </script>
    @endpush
@endsection
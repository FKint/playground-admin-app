@extends('layouts.app')

@push('styles')
<style>
    .twitter-typeahead {
        width: 100%;
    }
</style>
@endpush
@section('content')
    <h1>Registreren</h1>

    <form class="form-horizontal">
        <div class="form-group">
            <label for="week" class="col-sm-2 control-label">Selecteer week: </label>
            <div class="col-sm-10">
                <select id="week" name="week" class="form-control">
                    @foreach($all_weeks as $w)
                        <option value="{{ $w->id }}" @if($w->id == $week->id) selected @endif>
                            Week {{ $w->id }}: {{ $w->first_day_of_week }}
                            tot {{ $w->last_day()->date()->format('Y-m-d') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="q" class="col-sm-2 control-label">Zoek familie: </label>
            <div class="col-sm-10">
                <input type="search" id="family-search" name="q" class="typeahead form-control" placeholder="Search"
                       autocomplete="off" spellcheck="false">
            </div>
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
                        let label = "Familie " + data.id + ": " + data.guardian_first_name + " " + data.guardian_last_name;
                        if (data.children.length > 0) {
                            const children_names = _.map(data.children, function (c) {
                                return c.first_name + " " + c.last_name;
                            }).join(", ");
                            label += " (Kinderen: " + children_names + ")";
                        }
                        return '<div class="list-group-item"><a href="#">' + label + '</a></div>';
                    }
                }
            }).on('typeahead:selected', function (ev, suggestion) {
                const week_id = $('#week').val();
                window.location.href = '{{ route('show_edit_registration') }}?week_id=' + week_id + '&family_id=' + suggestion.id;
            }).focus();
            $(".tt-hint").addClass("form-control");
        });
    </script>
    @endpush
@endsection
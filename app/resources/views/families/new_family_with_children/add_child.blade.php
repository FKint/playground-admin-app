@extends('layouts.internal')

@section('content')

    <h1>Kind toevoegen</h1>
    <p>
        Voogd: <b>{{$family->guardian_first_name}} {{$family->guardian_last_name}}</b>
    </p>
    <h3>Huidige kinderen</h3>
    <ul id="current-children-list">
        @foreach($family->children as $child)
            <li>
                {{$child->first_name}} {{$child->last_name}} &nbsp;
                <a class="btn btn-xs btn-remove-child-family"
                   href="{{ route('internal.show_remove_child_from_new_family_with_children',
                   ['child'=>$child, 'family' => $family]) }}">Verwijder</a>
            </li>
        @endforeach
    </ul>
    <h3>Nieuw kind toevoegen</h3>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-child-form']) }}
    @include('forms.child')
    {{ Form::close() }}
    <h3>Bestaand kind toevoegen</h3>
    <form class="typeahead" role="search">
        <div class="form-group" dusk="child-search-typeahead">
            <input type="search" id="child-search" name="q" class="form-control" placeholder="Search"
                   autocomplete="off">
        </div>
    </form>
    @push('scripts')
        <script>
            $(function () {
                const age_groups = {!! $year->age_groups !!};
                const new_child_form = $('#new-child-form');
                new_child_form.find('input[name=first_name]').first().focus();
                new_child_form.find('input[name=birth_year]').change(function () {
                    const birth_year = parseInt($(this).val());
                    if (isNaN(birth_year))
                        return true;

                    function getYearFromDate(date_string) {
                        return parseInt(date_string.substring(0, 4));
                    }

                    for (let i = 0; i < age_groups.length; ++i) {
                        const start_year = getYearFromDate(age_groups[i].start_date);
                        const end_year = getYearFromDate(age_groups[i].end_date);
                        if (start_year <= birth_year && birth_year < end_year) {
                            new_child_form.find('select[name=age_group_id]').val(age_groups[i].id);
                        }
                    }
                });

                let engine = new Bloodhound({
                    remote: {
                        url: '{!! route('api.typeahead.child_suggestions_for_family', ['family' => $family]) !!}?q=%QUERY%',
                        wildcard: '%QUERY%'
                    },
                    datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                $('#child-search').typeahead({
                    hint: true,
                    highlight: true,
                    minLength: 1
                }, {
                    source: engine.ttAdapter(),
                    name: 'child-list',
                    display: function (data) {
                        return data.id + ': ' + data.first_name + ' ' + data.last_name;
                    },
                    templates: {
                        empty: [
                            '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
                        ],
                        suggestion: function (data) {
                            console.log(data);
                            return '<a href="#" class="list-group-item">' + data.first_name + ' '
                                + data.last_name + ' (Huidige voogd(en): ' + _.join(_.map(data.families, function(family_entry){ return family_entry.guardian_full_name; }, ', ')) + ')</a>';
                        }
                    }
                }).on('typeahead:select', function (event, suggestion) {
                    $.post('{!! route('api.add_child_to_family', ['family' => $family->id, 'child' => 'CHILD_ID']) !!}'
                        .replace('CHILD_ID', suggestion.id), {}, function (result) {
                        console.log('done');
                        location.reload();
                    }).done(function () {
                        console.log('done');
                    }).fail(function () {
                        alert('Adding child failed!');
                        console.log('failed!');
                    });

                });
            });
        </script>
    @endpush
@endsection

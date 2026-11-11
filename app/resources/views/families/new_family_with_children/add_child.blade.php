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
                <a class="btn btn-sm btn-link btn-remove-child-family"
                   href="{{ route('internal.show_remove_child_from_new_family_with_children',
                   ['child'=>$child, 'family' => $family]) }}">Verwijder</a>
            </li>
        @endforeach
    </ul>
    <h3>Nieuw kind toevoegen</h3>
    {{ html()->form()->class('')->id('new-child-form')->attributes(['dusk' => 'new-child-form'])->open() }}
    <x-form-contents.child />
    {{ html()->form()->close() }}
    <h3>Bestaand kind toevoegen</h3>
    <form class="autocomplete" role="search">
        <div class="row mb-3" dusk="child-search-autocomplete">
            <select id="child-search" name="q" class="form-control" placeholder="Search"></select>
        </div>
    </form>
    @push('scripts')
        <script type="module">
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

                autocomplete(document.getElementById('child-search'), {
                    url: '{!! route('api.typeahead.child_suggestions_for_family', ['family' => $family]) !!}',
                    display: data => data.id + ': ' + data.first_name + ' ' + data.last_name,
                    render: (data, esc) => esc(data.first_name + ' ' + data.last_name
                        + ' (Huidige voogd(en): ' + data.families.map(f => f.guardian_full_name).join(', ') + ')'),
                    onSelect: suggestion => {
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
                    }
                });
            });
        </script>
    @endpush
@endsection

@extends('layouts.app')

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
                   href="{{ route('show_remove_child_from_new_family_with_children',
                   ['child_id'=>$child->id, 'family_id' => $family->id]) }}">Verwijder</a>
            </li>
        @endforeach
    </ul>
    <h3>Nieuw kind toevoegen</h3>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-child-form']) }}
    @include('children.forms.child')
    {{ Form::close() }}
    <h3>Bestaand kind toevoegen</h3>
    <form class="typeahead" role="search">
        <div class="form-group">
            <input type="search" id="child-search" name="q" class="form-control" placeholder="Search"
                   autocomplete="off">
        </div>
    </form>
    @push('scripts')
    <script>
        $(function () {
            let engine = new Bloodhound({
                remote: {
                    url: '{!! route('getChildSuggestionsForFamily', ['family_id' => $family->id]) !!}?q=%QUERY%',
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
                            + data.last_name + '</a>';
                    }
                }
            }).on('typeahead:select', function (event, suggestion) {
                $.post('{!! route('addChildToFamily', ['family_id' => $family->id]) !!}', {
                    child_id: suggestion.id
                }, function (result) {
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

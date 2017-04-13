<h3 id="edit-child-families-form-title">Voogden</h3>
<table class="table" id="child-families-table">
    @foreach($child->child_families as $child_family)
        <tr>
            <td>{{ $child_family->family->id }}</td>
            <td>{{ $child_family->family->guardian_full_name() }}</td>
            <td>
                <button class="btn btn-xs btn-edit-family">Wijzigen</button>
                <button class="btn btn-xs btn-remove-family" data-child-family-id="{{$child_family->id}}">
                    Verwijderen
                </button>
            </td>
        </tr>
    @endforeach
</table>
<h4>Kind aan een bestaande voogd linken</h4>
<form class="typeahead" role="search">
    <div class="form-group">
        <input type="search" id="family-search" name="q" class="form-control" placeholder="Search" autocomplete="off">
    </div>
</form>
<script>
    $(document).ready(function () {
        function reloadEditChildFamiliesForm() {
            let container = $('#edit-child-families-form-title').parent();
            container.load(container.data('url'));
        }

        $('#child-families-table').on('click', '.btn-remove-family', function () {
            $.post('{!! route('removeChildFamily', ['child_id' => $child->id]) !!}', {
                child_family_id: $(this).data('child-family-id')
            }, function (result) {
                reloadEditChildFamiliesForm();
            });
        });
        let engine = new Bloodhound({
            remote: {
                url: '{!! route('getChildFamilySuggestions', ['child_id' => $child->id]) !!}?q=%QUERY%',
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
                    return '<a href="#" class="list-group-item">' + data.guardian_first_name + ' '
                        + data.guardian_last_name + '</a>';
                }
            }
        }).on('typeahead:select', function (event, suggestion) {
            $.post('{!! route('addChildFamily', ['child_id' => $child->id]) !!}', {
                family_id: suggestion.id
            }, function (result) {
                reloadEditChildFamiliesForm();
            }).fail(function () {
                alert('Adding family failed!');
                console.log('failed!');
            });
        });
    });
</script>

<h4>Nieuwe voogd toevoegen</h4>
<input title="Voogdnaam" type="text"/>

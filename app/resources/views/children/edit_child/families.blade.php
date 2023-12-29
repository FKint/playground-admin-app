<div id="existing-child-families">
    <h4>Huidige voogden</h4>
    <table class="table" id="child-families-table" dusk="current-families">
        <thead>
            <tr>
                <th>Voogd ID</th>
                <th>Naam</th>
                <th></th>
            </tr>
        </thead>
        @foreach($child->child_families as $child_family)
        <tr>
            <td>{{ $child_family->family->id }}</td>
            <td>{{ $child_family->family->guardian_full_name() }}</td>
            <td>
                <button class="btn btn-xs btn-edit-family" data-family-id="{{ $child_family->family->id }}">
                    Wijzigen
                </button>
                <button class="btn btn-xs btn-remove-family" data-family-id="{{$child_family->family->id}}">
                    Verwijderen
                </button>
            </td>
        </tr>
        @endforeach
    </table>
</div>

<div id="link-existing-child-family">
    <h4>Kind aan een bestaande voogd linken</h4>
    <form class="typeahead" role="search">
        <div class="form-group" dusk="family-search-typeahead">
            <input type="search" id="family-search" name="q" class="form-control" placeholder="Search"
                autocomplete="off">
        </div>
    </form>
</div>
<div id="link-new-child-family">
    <h4>Kind aan een nieuwe voogd linken</h4>
    {{ html()->form()->class('form-horizontal')->id('link-new-family').attributes(['dusk' => 'link-new-family'])->open() }}
    <div id="link-new-family-error" class="form-group hidden alert alert-danger">
        <span class="text-danger" id="link-new-family-error-text"></span>
    </div>
    @include('forms.family', ['submit_text' => 'Voogd toevoegen'])
    {{ html()->form()->close() }}
</div>

<script>
    function reloadEditChildFamiliesDiv() {
        const container = $('#existing-child-families').parent();
        container.load(container.data('url'));
    }

    $(function () {
        const child_families_table_element = $('#child-families-table');
        child_families_table_element.on('click', '.btn-remove-family', function () {
            $.post('{!! route('api.remove_family_from_child', ['child' => $child, 'family' => 'FAMILY_ID']) !!}'
                .replace('FAMILY_ID', $(this).data('family-id')), {}, function (result) {
                reloadEditChildFamiliesDiv();
            });
        });

        let engine = new Bloodhound({
            remote: {
                url: '{!! route('api.typeahead.family_suggestions_for_child', ['child' => $child]) !!}?q=%QUERY%',
                wildcard: '%QUERY%'
            },
            datumTokenizer: Bloodhound.tokenizers.whitespace('q'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 10
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
                    return '<a class="list-group-item" href="#">' + label + '</a>';
                }
            }
        }).on('typeahead:selected', function (event, suggestion) {
            $.post('{!! route('api.add_family_to_child', ['child' => $child, 'family' => 'FAMILY_ID']) !!}'
                .replace('FAMILY_ID', suggestion.id), {}, function (result) {
                reloadEditChildFamiliesDiv();
            }).done(function () {
                console.log('done');
            }).fail(function () {
                alert('Adding family failed!');
                console.log('failed!');
            });
        }).focus();
        $(".tt-hint").addClass("form-control");

        child_families_table_element.on('click', '.btn-edit-family', function () {
            const family_id = $(this).data('family-id');
            showEditFamilyModal(family_id);
        });
    });
    $(function () {
        const form = $('#link-new-family');
        form.submit(function (event) {
            event.preventDefault();
            $('#link-new-family-error').addClass('hidden');
            $.post(
                '{!! route('internal.submit_link_new_child_family_form', ['child' => $child]) !!}',
                form.serializeArray()
            ).done(function () {
                reloadEditChildFamiliesDiv();
            }).fail(function (data) {
                let error_message = data.responseJSON.message;
                for(const error in data.responseJSON.errors){
                    error_message += " " + data.responseJSON.errors[error];
                }
            $('#link-new-family-error-text').text('Fout! ' + error_message);
            $('#link-new-family-error').removeClass('hidden');
            });
        });
    });
</script>
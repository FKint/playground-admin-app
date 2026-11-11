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
            <td class="text-nowrap">
                <button class="btn btn-sm btn-outline-secondary btn-edit-family" data-family-id="{{ $child_family->family->id }}">
                    Wijzigen
                </button>
                <button class="btn btn-sm btn-outline-secondary btn-remove-family" data-family-id="{{$child_family->family->id}}">
                    Verwijderen
                </button>
            </td>
        </tr>
        @endforeach
    </table>
</div>

<div id="link-existing-child-family">
    <h4>Kind aan een bestaande voogd linken</h4>
    <form class="autocomplete" role="search">
        <div class="row mb-3" dusk="family-search-autocomplete">
            <select id="family-search" name="q" class="form-control" placeholder="Search"></select>
        </div>
    </form>
</div>
<div id="link-new-child-family">
    <h4>Kind aan een nieuwe voogd linken</h4>
    {{ html()->form()->id('link-new-family')->attributes(['dusk' => 'link-new-family'])->open() }}
    <div id="link-new-family-error" class="row mb-3 hidden alert alert-danger">
        <span class="text-danger" id="link-new-family-error-text"></span>
    </div>
    <x-form-contents.family submit-text="Voogd toevoegen" />
    {{ html()->form()->close() }}
</div>

<script type="module">
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

        const family_search = autocomplete(document.getElementById('family-search'), {
            url: '{!! route('api.typeahead.family_suggestions_for_child', ['child' => $child]) !!}',
            render: (data, esc) => {
                let label = "Familie " + data.id + ": " + data.guardian_first_name + " " + data.guardian_last_name;
                if (data.children.length > 0) {
                    label += " (Kinderen: " + data.children.map(c => c.first_name + " " + c.last_name).join(", ") + ")";
                }
                return esc(label);
            },
            onSelect: suggestion => {
                $.post('{!! route('api.add_family_to_child', ['child' => $child, 'family' => 'FAMILY_ID']) !!}'
                    .replace('FAMILY_ID', suggestion.id), {}, function (result) {
                    reloadEditChildFamiliesDiv();
                }).done(function () {
                    console.log('done');
                }).fail(function () {
                    alert('Adding family failed!');
                    console.log('failed!');
                });
            }
        });
        family_search.focus();

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
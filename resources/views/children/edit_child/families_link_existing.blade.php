<h4>Kind aan een bestaande voogd linken</h4>
<form class="typeahead" role="search">
    <div class="form-group">
        <input type="search" id="family-search" name="q" class="form-control" placeholder="Search" autocomplete="off">
    </div>
</form>

<script>
    $(document).ready(function () {
        function reloadEditChildFamiliesDiv() {
            const container = $('#edit-families-div');
            container.load(container.data('url'));
        }

        $('#child-families-table').on('click', '.btn-remove-family', function () {
            $.post('{!! route('removeChildFamily', ['child_id' => $child->id]) !!}', {
                child_family_id: $(this).data('child-family-id')
            }, function (result) {
                reloadEditChildFamiliesDiv();
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
            $.post('{!! route('addChildFamily', ['child_id' => $child->id]) !!}', {
                family_id: suggestion.id
            }, function (result) {
                reloadEditChildFamiliesDiv();
            }).done(function () {
                console.log('done');
            }).fail(function () {
                alert('Adding family failed!');
                console.log('failed!');
            });
        });
        $(".tt-hint").addClass("form-control");
    });
</script>
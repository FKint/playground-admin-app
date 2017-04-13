<h3>Voogden</h3>
<div id="existing-child-families">
    <h4>Huidige voogden</h4>
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
</div>
<div id="link-existing-child-family">
    @include('children.edit_child.families_link_existing')
</div>
<div id="link-new-child-family">

</div>
<script>
    $(function () {
        const url = '{!! route('show_link_new_child_family_form', ['child_id' => $child->id]) !!}';
        $('#link-new-child-family').load(url);
    });
</script>
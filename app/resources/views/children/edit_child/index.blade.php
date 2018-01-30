@extends('layouts.internal')

@section('title')
    Kind wijzigen
@endsection
@section('content')

    <h1>Kind bijwerken</h1>
    @include('children.edit_child.content')

@endsection

@push('scripts')
<script>
    $(function () {
        const edit_child_form_url = '{!! route('edit_child_form') !!}' + '?child_id={{ $child->id }}';
        $('#edit-child-info-div')
            .data('url', edit_child_form_url)
            .load(edit_child_form_url);

        const edit_child_families_form_url = '{!! route('edit_child_families_form') !!}' + '?child_id={{ $child->id }}';
        $('#edit-child-families-div')
            .data('url', edit_child_families_form_url)
            .load(edit_child_families_form_url);
    });
</script>
@endpush
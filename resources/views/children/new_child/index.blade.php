@extends('layouts.app')

@section('content')

    <h1>Nieuw kind toevoegen</h1>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-child-form']) }}
    @include('children.forms.child')
    {{ Form::close() }}

@endsection

@push('scripts')
<script>
    $(function () {
        $('#new-child-form').find('input[name=first_name]').first().focus();
    });
</script>
@endpush
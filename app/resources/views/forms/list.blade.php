@extends('forms.form')

@php
    $isReadOnly = (isset($readonly) && $readonly);
@endphp

@section('form-content')
    @if(isset($with_id) && $with_id)
        <x-form-elements.text name="id" readonly />
    @endif
    <x-form-elements.text name="name" :readonly="$isReadOnly" />
    <x-form-elements.number name="price" pattern="[0-9]+([\\.,][0-9]+)?" step="0.01" :readonly="$isReadOnly" />
    <x-form-elements.text name="date" :readonly="$isReadOnly" id="txt-list-date" />
    <x-form-elements.checkbox name="show_on_attendance_form" :readonly="$isReadOnly" />
    <x-form-elements.checkbox name="show_on_dashboard" :readonly="$isReadOnly" />
    @if(!$isReadOnly)
        <x-form-elements.submit :text="isset($submit_text) ? $submit_text : 'Opslaan'" />
    @endif
@endsection
@push('scripts')
    <script>
        $(function () {
            $('#txt-list-date').datepicker({format: "yyyy-mm-dd"});
        });
    </script>
@endpush
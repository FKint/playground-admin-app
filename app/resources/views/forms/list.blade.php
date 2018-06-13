@extends('forms.form')

@section('form-content')
    @if(isset($with_id) && $with_id)
        {{ Form::bsText('id', ['readonly']) }}
    @endif
    {{ Form::bsText('name', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsNumber('price', array_merge(['pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01'], (isset($readonly) && $readonly)?['readonly']:[])) }}
    {{ Form::bsText('date', array_merge(['id' => 'txt-list-date'],(isset($readonly) && $readonly)?['readonly']:[])) }}
    {{ Form::bsCheckbox('show_on_attendance_form',  (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsCheckbox('show_on_dashboard', (isset($readonly) && $readonly)?['readonly']:[]) }}
    @if(!isset($readonly) || !$readonly)
        {{ Form::bsSubmit(isset($submit_text) ? $submit_text : "Opslaan") }}
    @endif
@endsection
@push('scripts')
    <script>
        $(function () {
            $('#txt-list-date').datepicker({format: "yyyy-mm-dd"});
        });
    </script>
@endpush
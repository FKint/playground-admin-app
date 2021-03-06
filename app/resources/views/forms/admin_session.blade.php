@extends('forms.form')

@section('form-content')
    {{ Form::bsText('responsible_name', 'Naam verantwoordelijke', (isset($readonly) && $readonly)?['readonly']:[]) }}
    {{ Form::bsNumber('counted_cash', 'Kassatelling', array_merge(['pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01'], (isset($readonly) && $readonly)?['readonly']:[])) }}
    {{ Form::bsTextarea('remarks', 'Opmerkingen', (isset($readonly) && $readonly)?['readonly']:[]) }}
    @if(!isset($readonly) || !$readonly)
        {{ Form::bsSubmit() }}
    @endif
@endsection
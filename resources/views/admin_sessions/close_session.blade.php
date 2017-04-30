@extends('layouts.app')

@section('content')
    <h3>Kassa afsluiten</h3>
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'closes-admin-session-form']) }}
    {{ Form::bsText('responsible_name') }}
    {{ Form::bsNumber('counted_cash', ['id' => 'counted-cash', 'pattern'=>"[0-9]+([\\.,][0-9]+)?", 'step'=>'0.01']) }}
    {{ Form::bsText('remarks') }}
    {{ Form::bsSubmit() }}
    {{ Form::close() }}
@endsection
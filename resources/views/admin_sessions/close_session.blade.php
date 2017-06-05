@extends('layouts.app')

@section('content')
    <h3>Kassa afsluiten</h3>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'closes-admin-session-form']) }}
    @include('forms.admin_session')
    {{ Form::close() }}
@endsection
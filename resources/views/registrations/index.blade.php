@extends('layouts.app')

@section('content')
    <h1>Registraties</h1>

    <div class="row">
        <a href="{{ route('show_find_family_registration', ['week_id' => $playground_day->week->id]) }}"
           class="btn btn-primary">Registreer betalingen/aanwezigheid</a>
    </div>
@endsection
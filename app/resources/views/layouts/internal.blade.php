@extends('layouts.app')
@include('modals.global')

@section('navbar-items')
    <li class="nav-item"><a class="nav-link {{ ($selected_menu_item ?? null) === 'dashboard' ? 'active' : '' }}" href="{{route('internal.dashboard')}}">Dashboard</a></li>
    <li class="nav-item"><a class="nav-link {{ ($selected_menu_item ?? null) === 'children' ? 'active' : '' }}" href="{{route('internal.children')}}">Kinderen</a></li>
    <li class="nav-item"><a class="nav-link {{ ($selected_menu_item ?? null) === 'families' ? 'active' : '' }}" href="{{route('internal.families')}}">Voogden</a></li>
    <li class="nav-item"><a class="nav-link {{ ($selected_menu_item ?? null) === 'registrations' ? 'active' : '' }}" href="{{route('internal.registrations')}}">Registraties</a></li>
    <li class="nav-item"><a class="nav-link {{ ($selected_menu_item ?? null) === 'lists' ? 'active' : '' }}" href="{{ route('internal.lists') }}">Lijsten</a></li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button"
           aria-expanded="false">Extra</a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{route('internal.settings')}}">Instellingen</a></li>
            <li><a class="dropdown-item" href="{{ route('logout_and_redirect') }}">Uitloggen</a></li>
        </ul>
    </li>
@endsection

@section('year-descriptor')
    @if(!empty($year))
        {{ $year->description }}
    @endif
@endsection
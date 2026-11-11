@extends('layouts.app')
@include('modals.global')

@section('navbar-items')
    <li class="nav-item"><a class="nav-link @if(!empty($selected_menu_item) && $selected_menu_item == 'dashboard')active@endif" href="{{route('internal.dashboard')}}">Dashboard</a></li>
    <li class="nav-item"><a class="nav-link @if(!empty($selected_menu_item) && $selected_menu_item == 'children')active@endif" href="{{route('internal.children')}}">Kinderen</a></li>
    <li class="nav-item"><a class="nav-link @if(!empty($selected_menu_item) && $selected_menu_item == 'families')active@endif" href="{{route('internal.families')}}">Voogden</a></li>
    <li class="nav-item"><a class="nav-link @if(!empty($selected_menu_item) && $selected_menu_item == 'registrations')active@endif" href="{{route('internal.registrations')}}">Registraties</a></li>
    <li class="nav-item"><a class="nav-link @if(!empty($selected_menu_item) && $selected_menu_item == 'lists')active@endif" href="{{ route('internal.lists') }}">Lijsten</a></li>
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
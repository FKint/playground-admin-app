@extends('layouts.app')
@include('modals.global')

@section('navbar-items')
    <li @if(!empty($selected_menu_item) && $selected_menu_item == 'dashboard')class="active"@endif><a
                href="{{route('internal.dashboard')}}">Dashboard</a></li>
    <li @if(!empty($selected_menu_item) && $selected_menu_item == 'children')class="active"@endif>
        <a href="{{route('internal.children')}}">Kinderen</a>
    </li>
    <li @if(!empty($selected_menu_item) && $selected_menu_item == 'families')class="active"@endif>
        <a href="{{route('internal.families')}}">Voogden</a>
    </li>
    <li @if(!empty($selected_menu_item) && $selected_menu_item == 'registrations')class="active"@endif>
        <a href="{{route('internal.registrations')}}">Registraties</a>
    </li>
    <li @if(!empty($selected_menu_item) && $selected_menu_item == 'lists')class="active"@endif>
        <a href="{{ route('internal.lists') }}">Lijsten</a>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
           aria-expanded="false">Extra<span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><a href="{{route('internal.settings')}}">Instellingen</a></li>
            <li><a href="{{ route('logout_and_redirect') }}">Uitloggen</a></li>
        </ul>
    </li>
@endsection
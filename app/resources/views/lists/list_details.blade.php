@extends('layouts.internal')
@section('title')
    Lijst {{ $list->id }}: {{ $list->name }}
    @if($list->date && $list->price)
        ({{ $list->date }} - € {{ $list->price }})
    @elseif($list->date)
        ({{ $list->date }})
    @elseif($list->price)
        (€ {{ $list->price }})
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12 col-lg-6">
            <h2>Details</h2>
            @include('lists.list_details.settings')
        </div>
        <div class="col-xs-12 col-lg-6">
            @include('lists.list_details.participants')
        </div>
    </div>
@endsection
@extends('layouts.internal')

@section('title')
    Registraties {{ $date->format('d-m-Y') }}
@endsection
@section('content')
    <h1>Registraties</h1>

    <div class="row">
        <div class="col-xs-1 col-lg-1">
            <button class="btn btn-default pull-right">
                <span class="glyphicon glyphicon-backward" id="btn-prev-day"></span>
            </button>
        </div>
        <div class="col-xs-3 col-lg-3">
            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy/mm/dd"
                 id="registration-datepicker" dusk="registration-datepicker">
                <input type="text" class="form-control">
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div>
        </div>
        <div class="col-xs-1 col-lg-1">
            <button class="btn btn-default pull-left" id="btn-next-day">
                <span class="glyphicon glyphicon-forward"></span>
            </button>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    @if(!empty($playground_day))
        <div class="row">
            <a href="{{ route('internal.show_find_family_registration', ['week' => $playground_day->week]) }}"
               class="btn btn-primary">Registreer betalingen/aanwezigheid</a>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            @include('registrations.table')
        </div>
    @else
        <div class="row">
            <div class="alert alert-warning">Er is geen werking op deze datum.</div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    $(function () {
        function goToMoment(m) {
            window.location.href = "{{ route('internal.registrations_for_date', ['date' => 'DATE']) }}".replace('DATE', m.format('YYYY-MM-DD'));
        }

        const default_date = new Date({{ $date->format('Y') }}, {{ $date->format('m') }} - 1, {{ $date->format('d') }});
        $('#registration-datepicker').datepicker()
            .datepicker('update', default_date)
            .on('changeDate', function (event) {
                console.log(event.date);
                goToMoment(moment(event.date));
            });
        $('#btn-prev-day').click(function () {
            goToMoment(moment(default_date).subtract(1, 'days'));
        });
        $('#btn-next-day').click(function () {
            goToMoment(moment(default_date).add(1, 'days'));
        });
    });

</script>
@endpush

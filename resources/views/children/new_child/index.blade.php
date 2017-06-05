@extends('layouts.app')

@section('content')

    <h1>Nieuw kind toevoegen</h1>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{ Form::open(['class' => 'form-horizontal', 'id' => 'new-child-form']) }}
    @include('forms.child')
    {{ Form::close() }}

@endsection

@push('scripts')
<script>
    $(function () {
        const age_groups = {!! $all_age_groups !!};
        const new_child_form = $('#new-child-form');
        new_child_form.find('input[name=first_name]').first().focus();
        new_child_form.find('input[name=birth_year]').change(function () {
            new_child_form.find('select[name=age_group_id]').val(0);
            const birth_year = parseInt($(this).val());
            if (isNaN(birth_year))
                return true;

            function getYearFromDate(date_string) {
                return parseInt(date_string.substring(0, 4));
            }

            for (let i = 0; i < age_groups.length; ++i) {
                const start_year = getYearFromDate(age_groups[i].start_date);
                const end_year = getYearFromDate(age_groups[i].end_date);
                if (start_year <= birth_year && birth_year < end_year) {
                    console.log(start_year + "<=" + birth_year + "<" + end_year);
                    new_child_form.find('select[name=age_group_id]').val(age_groups[i].id);
                    return;
                }
            }
        });
    });
</script>
@endpush
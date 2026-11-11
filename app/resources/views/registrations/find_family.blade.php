@extends('layouts.internal')

@section('content')
    <h1>Registreren</h1>

    <form class="">
        <div class="row mb-3">
            <label for="week" class="col-sm-2 col-form-label">Selecteer week: </label>
            <div class="col-sm-10">
                <select id="week" name="week" class="form-select" dusk="week">
                    @foreach($year->weeks as $w)
                        <option value="{{ $w->id }}" @if($w->id == $week->id) selected @endif>
                            Week {{ $w->id }}: {{ $w->first_day()->date()->format('d-m-Y') }}
                            tot {{ $w->last_day()->date()->format('d-m-Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3" dusk="family-search-autocomplete">
            <label for="q" class="col-sm-2 col-form-label">Zoek familie: </label>
            <div class="col-sm-10">
                <select id="family-search" name="q" class="form-control" placeholder="Search"></select>
            </div>
        </div>
    </form>

    @push('scripts')
        <script type="module">
            $(function () {
                const family_search = autocomplete(document.getElementById('family-search'), {
                    url: '{!! route('api.typeahead.family_suggestions') !!}',
                    display: data => data.id + ': ' + data.guardian_first_name + ' ' + data.guardian_last_name,
                    render: (data, esc) => {
                        let label = "Familie " + data.id + ": " + data.guardian_first_name + " " + data.guardian_last_name;
                        if (data.children.length > 0) {
                            label += " (Kinderen: " + data.children.map(c => c.first_name + " " + c.last_name).join(", ") + ")";
                        }
                        return esc(label);
                    },
                    onSelect: suggestion => {
                        const week_id = $('#week').val();
                        window.location.href = '{{ route('internal.show_edit_registration', ['week' => 'WEEK_ID', 'family' => 'FAMILY_ID']) }}'
                            .replace('WEEK_ID', week_id)
                            .replace('FAMILY_ID', suggestion.id);
                    }
                });
                family_search.focus();
            });
        </script>
    @endpush
@endsection
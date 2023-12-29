<x-form-helpers.base-form-content>
    @if(isset($withId) && $withId)
        <x-form-elements.text name="id" display-name="ID" readonly />
    @endif
    <x-form-elements.text name="name" display-name="Lijstnaam" :readonly="$readonly" />
    <x-form-elements.number name="price" display-name="Prijs" pattern="[0-9]+([\\.,][0-9]+)?" step="0.01" :readonly="$readonly" />
    <x-form-elements.text name="date" display-name="Datum" :readonly="$readonly" id="txt-list-date" />
    <x-form-elements.checkbox name="show_on_attendance_form" display-name="Aanwezigheidslijst" :readonly="$readonly" />
    <x-form-elements.checkbox name="show_on_dashboard" display-name="Dashboard" :readonly="$readonly" />
    @if(!$readonly)
        <x-form-elements.submit :text="$submitText" />
    @endif
</x-form-helpers.base-form-content>

@push('scripts')
    <script>
        $(function () {
            const datepicker = $('#txt-list-date').datepicker({format: "yyyy-mm-dd"});
        });
    </script>
@endpush
<x-form-helpers.base-form-content>
    <x-form-elements.text name="first_name" display-name="Voornaam" :readonly="$readonly" />
    <x-form-elements.text name="last_name" display-name="Naam" :readonly="$readonly" />
    <x-form-elements.number name="birth_year" display-name="Geboortejaar" :readonly="$readonly" />
    <x-form-elements.dropdown name="age_group_id" display-name="Werking"
        :choices="['0' => 'Werking'] + $year->getAllAgeGroupsById()" :readonly="$readonly" />
    <x-form-elements.text-area name="remarks" display-name="Opmerkingen" :readonly="$readonly" />
    @if(!$readonly)
        <x-form-elements.submit />
    @endif
</x-form-helpers.base-form-content>
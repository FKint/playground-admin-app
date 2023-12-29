<x-form-helpers.base-form-content>
    @if($withId)
        <x-form-elements.text name="id" display-name="ID" readonly />
    @endif
    <x-form-elements.text name="guardian_first_name" display-name="Voornaam" :readonly="$readonly" />
    <x-form-elements.text name="guardian_last_name" display-name="Naam" :readonly="$readonly" />
    <x-form-elements.dropdown name="tariff_id" display-name="Tarief"
        :choices="$year->getAllTariffsById()->all()" :readonly="$readonly" />
    <x-form-elements.forced-choice-radio name="needs_invoice" display-name="Betalingswijze"
        :choices="['0' => 'Cash', '1' => 'Factuur']" :readonly="$readonly" />
    <x-form-elements.text name="email" display-name="E-mail" :readonly="$readonly" />
    <x-form-elements.text-area name="remarks" display-name="Opmerkingen" :readonly="$readonly" />
    <x-form-elements.text-area name="contact" display-name="Contactgegevens" :readonly="$readonly" />
    <x-form-elements.text-area name="social_contact" display-name="Contact sociaal tarief" :readonly="$readonly" />
    @if(!$readonly)
        <x-form-elements.submit :text="$submitText" />
    @endif
</x-form-helpers.base-form-content>
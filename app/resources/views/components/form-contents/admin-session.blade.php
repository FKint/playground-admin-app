<x-form-helpers.base-form-content>
    <x-form-elements.text name="responsible_name" display-name="Naam verantwoordelijke" :readonly="$readonly" />
    <x-form-elements.number name="counted_cash" display-name="Kassatelling" :readonly="$readonly" pattern="[0-9]+([\\.,][0-9]+)?" step="0.01" />
    <x-form-elements.text-area name="remarks" display-name="Opmerkingen" :readonly="$readonly" />
    @if(!$readonly)
        <x-form-elements.submit />
    @endif
</x-form-helpers.base-form-content>
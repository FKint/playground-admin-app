<div class="row">
    <div class="col-xs-12">
        <table class="table table-bordered" id="family-children-table" dusk="family-children-table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Naam</th>
                <th>Geboortejaar</th>
                <th>Werking</th>
                <th>Belangrijk</th>
                <th>Factuur</th>
            </tr>
            </thead>
            <tbody>
            @foreach($family->children as $child)
                <tr>
                    <td>{{ $child->first_name }}</td>
                    <td>{{ $child->last_name }}</td>
                    <td>{{ $child->birth_year }}</td>
                    <td>{{ $child->age_group->name }}</td>
                    <td>{{ $child->remarks }}</td>
                    <td>
                        <a 
                            class="btn-child-family-invoice" 
                            data-family-id="{{ $family->id }}" 
                            data-child-id="{{ $child->id }}"
                            href="{{ route('internal.show_child_family_invoice_pdf', ['child' => $child->id, 'family' => $family->id]) }}">Invoice</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
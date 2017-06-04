<div class="row">
    <div class="col-xs-12">
        <table class="table table-bordered" id="family-children-table">
            <thead>
            <tr>
                <th>Voornaam</th>
                <th>Naam</th>
                <th>Geboortejaar</th>
                <th>Werking</th>
                <th>Belangrijk</th>
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
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<ul class="nav nav-tabs" role="tablist" id="info-child-tablist">
    <li role="presentation" class="nav-item">
        <a href="#info-child-info-div" class="nav-link active" aria-controls="info" role="tab" data-bs-toggle="tab">Info</a>
    </li>
    <li role="presentation" class="nav-item">
        <a href="#info-child-families-div" class="nav-link" aria-controls="families" role="tab" data-bs-toggle="tab">Voogden</a>
    </li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane fade show active" id="info-child-info-div">
        {{ html()->modelForm($child)->id('info-child-form')->open() }}
        <x-form-contents.child readonly />
        {{ html()->closeModelForm() }}
    </div>
    <div role="tabpanel" class="tab-pane fade" id="info-child-families-div">
        @foreach($child->child_families as $child_family)
            <h4>Voogd {{ $child_family->family->guardian_full_name() }}</h4>
            {{ html()->modelForm($child_family->family)->open() }}
            <x-form-contents.family readonly with-id />
            {{ html()->closeModelForm() }}
        @endforeach
    </div>
</div>

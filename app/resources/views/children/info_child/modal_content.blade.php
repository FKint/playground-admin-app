<ul class="nav nav-tabs" role="tablist" id="info-child-tablist">
    <li role="presentation" class="active">
        <a href="#info-child-info-div" aria-controls="info" role="tab" data-toggle="tab">Info</a>
    </li>
    <li role="presentation">
        <a href="#info-child-families-div" aria-controls="families" role="tab" data-toggle="tab">Voogden</a>
    </li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="info-child-info-div">
        {{ html()->modelForm($child)->class('form-horizontal')->id('info-child-form')->open() }}
        <x-form-contents.child readonly />
        {{ html()->closeModelForm() }}
    </div>
    <div role="tabpanel" class="tab-pane" id="info-child-families-div">
        @foreach($child->child_families as $child_family)
            <h4>Voogd {{ $child_family->family->guardian_full_name() }}</h4>
            {{ html()->modelForm($child_family->family)->class('form-horizontal')->open() }}
            <x-form-contents.family readonly with-id />
            {{ html()->closeModelForm() }}
        @endforeach
    </div>
</div>

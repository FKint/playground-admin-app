{{ html()->modelForm($list, method: 'POST', action: route('internal.submit_edit_list', ['list' => $list]))
    ->id('edit-list-form')->attributes(['dusk' => 'edit-list-form'])
    ->open() }}
<x-form-contents.activity-list with-id />
{{ html()->closeModelForm() }}
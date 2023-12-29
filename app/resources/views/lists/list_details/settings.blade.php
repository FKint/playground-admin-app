{{ html()->modelForm($list, action: route('internal.submit_edit_list', ['list' => $list]))
    ->class('form-horizontal')->id('edit-list-form')->attributes(['dusk' => 'edit-list-form']) }}
@include('forms.list')
{{ html()->closeModelForm() }}
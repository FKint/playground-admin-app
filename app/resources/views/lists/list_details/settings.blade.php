{{ Form::model($list, [
    'class' => 'form-horizontal',
    'id' => 'edit-list-form',
    'dusk' => 'edit-list-form',
    'url' => route('internal.submit_edit_list', ['list' => $list])]) }}
@include('forms.list')
{{ Form::close() }}
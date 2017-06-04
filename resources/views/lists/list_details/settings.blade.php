{{ Form::model($list, ['class' => 'form-horizontal', 'id' => 'edit-list-form', 'url' => route('submit_edit_list', ['list_id' => $list->id])]) }}
@include('forms.list')
{{ Form::close() }}
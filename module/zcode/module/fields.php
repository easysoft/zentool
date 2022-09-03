<?php
$fields = array();
$fields['createdBy']['name']    = 'createdBy';
$fields['createdBy']['label']   = '由谁创建';
$fields['createdBy']['control'] = 'select';
$fields['createdBy']['options'] = 'user';
$fields['createdBy']['default'] = '';

$fields['name']['name']    = 'name';
$fields['name']['label']   = '名称';
$fields['name']['control'] = 'input';
$fields['name']['options'] = '';
$fields['name']['default'] = '';

$fields['status']['name']    = 'status';
$fields['status']['label']   = '状态';
$fields['status']['control'] = 'select';
$fields['status']['options'] = json_encode(array('active' => '激活', 'closed' => '已关闭'));
$fields['status']['default'] = 'active';

$actionFields = array();
foreach($fields as $field) $actionFields['view'][$field['name']] = (object) $field;
foreach($actionFields['view'] as $field => $setting)
{
   $setting->field   = $field;
   $setting->show    = 1;
   $setting->width   = 'auto';
   $setting->position = 'basic';
   $setting->defaultValue = '';
   $setting->rules = '';
}

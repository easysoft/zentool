<?php
$config->fields['createdBy']['name']    = 'createdBy';
$config->fields['createdBy']['label']   = '由谁创建';
$config->fields['createdBy']['control'] = 'select';
$config->fields['createdBy']['options'] = 'user';
$config->fields['createdBy']['default'] = '';

$config->fields['name']['name']    = 'name';
$config->fields['name']['label']   = '名称';
$config->fields['name']['control'] = 'input';
$config->fields['name']['options'] = '';
$config->fields['name']['default'] = '';

$config->fields['status']['name']    = 'status';
$config->fields['status']['label']   = '状态';
$config->fields['status']['control'] = 'select';
$config->fields['status']['options'] = json_encode(array('active' => '激活', 'closed' => '已关闭'));
$config->fields['status']['default'] = 'active';

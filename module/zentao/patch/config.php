<?php
$config->arguments['--local'] = 'local';
$config->arguments['-r']      = 'revert';
$config->arguments['-b']      = 'build';
$config->arguments['-rel']    = 'release';

$config->patch->paramKey['view']    = 'patchID';
$config->patch->paramKey['install'] = 'patchID';
$config->patch->paramKey['revert']  = 'patchID';
$config->patch->paramKey['release'] = 'patchPath';

$config->patch->showFields = new stdclass();
$config->patch->showFields->list = array('id', 'type', 'code', 'name', 'date', 'installed');

$config->patch->nameTpl     = 'zentao.%s.%d.zip';
$config->patch->webStoreUrl = 'http://www.zentao.net/';
$config->patch->buildFields = 'version,type,id,author,desc,changelog,license,buildPath';
$config->patch->ztcliTpl    = '%s' . DS . 'bin' . DS . 'ztcli ' . 'http://localhost/action-create-patch-%s-%s.json pathinfo';

<?php
$config->arguments['--local'] = 'local';
$config->arguments['-r']      = 'revert';
$config->arguments['-b']      = 'build';
$config->arguments['-rel']    = 'release';

$config->patch->paramKey['view']    = 'patchID';
$config->patch->paramKey['install'] = 'patchID';
$config->patch->paramKey['revert']  = 'patchID';
$config->patch->paramKey['release'] = 'patchPath';

$config->patch->nameTpl = 'zentao.%s.%d.zip';

$config->patch->webStoreUrl = 'http://zentao.cn/';
$config->patch->buildFields = 'version,type,id,author,desc,changelog,license,buildPath';

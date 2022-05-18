<?php
$config->arguments['-l']      = 'local';
$config->arguments['patch-l'] = 'list';
$config->arguments['--local'] = 'local';
$config->arguments['local']   = 'local';
$config->arguments['-r']      = 'revert';

$config->patch->paramKey['view']    = 'patchID';
$config->patch->paramKey['install'] = 'patchID';
$config->patch->paramKey['revert']  = 'patchID';

$config->patch->nameTpl = 'zentao.%s.%s.%d.zip';

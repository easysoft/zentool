<?php
$config->arguments['-l']      = 'local';
$config->arguments['--local'] = 'local';
$config->arguments['local']   = 'local';

$config->patch->paramKey['view']    = 'patchID';
$config->patch->paramKey['install'] = 'patchID';

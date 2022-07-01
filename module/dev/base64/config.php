<?php
$config->arguments['-d'] = 'decode';
$config->arguments['-e'] = 'encode';

$config->base64 = new stdClass();
$config->base64->paramKey['encode'] = 'str';
$config->base64->paramKey['decode'] = 'str';

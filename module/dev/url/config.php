<?php
$config->arguments['-d'] = 'decode';
$config->arguments['-e'] = 'encode';

$config->url = new stdClass();
$config->url->paramKey['encode'] = 'str';
$config->url->paramKey['decode'] = 'str';

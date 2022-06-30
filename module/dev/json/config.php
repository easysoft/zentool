<?php
$config->arguments['-d']            = 'decode';
$config->arguments['-a']            = 'associative';
$config->arguments['--associative'] = 'associative';

$config->json = new stdClass();
$config->json->paramKey['decode'] = 'param';

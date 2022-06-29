<?php
$config->arguments['-n'] = 'new';
$config->arguments['-d'] = 'diff';
$config->arguments['-v'] = 'view';

$config->basic = new stdClass();
$config->basic->diff = new stdClass();
$config->basic->diff->fields = 'new,diff';

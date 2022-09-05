<?php
$config->ext->paramKey['init-m'] = 'moduleName';
$config->ext->paramKey['init-t'] = 'type';
$config->ext->paramKey['init-f'] = 'functionList';

$config->ext = new stdclass;
$config->ext->template = new stdclass;
$config->ext->template->control = <<<EOT
<?php
helper::importControl('%s');
class %s extends attend %s
{
%s
}
EOT;

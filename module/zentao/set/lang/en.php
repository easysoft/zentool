<?php
/**
 * The index module english file of ZenTaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
$lang->set = new stdclass();
$lang->set->dirTip = <<<EOF
Setting the zentao configuration, please press ctrl + c to exit.

The zentao root directory, eg /opt/zentao:
EOF;

$lang->set->dirNotExists  = 'The directory %s is not a zentao instance, please check again:';
$lang->set->tryTimeLimit  = 'Please re-execute and enter';
$lang->set->noWriteAccess = 'Unable to open the config file!';
$lang->set->saveSuccess   = 'Saved successfully.';

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
$lang->set->inputDir = <<<EOF
Setting the zentao configuration, please press ctrl + c to exit.

The zentao root directory, eg /opt/zentao:

EOF;
$lang->set->dirNotExists  = 'The directory %s is not a zentao instance, please check again:' . PHP_EOL;
$lang->set->tryTimeLimit  = 'Please re execute and enter' . PHP_EOL;
$lang->set->noWriteAccess = 'Unable to open config file!' . PHP_EOL;
$lang->set->saveSuccess   = 'Saved successfully.' . PHP_EOL;

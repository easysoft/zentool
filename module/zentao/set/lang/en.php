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
$lang->set->urlTip     = 'The zentao host url, eg https://zentaopms.com:' . PHP_EOL;
$lang->set->accountTip = 'username:' . PHP_EOL;
$lang->set->pwdTip     = 'password:' . PHP_EOL;
$lang->set->checking   = 'Checking...' . PHP_EOL;
$lang->set->logging    = 'Logging...' . PHP_EOL;

$lang->set->dirNotExists  = 'The directory %s is not a zentao instance, please check again:' . PHP_EOL;
$lang->set->tryTimeLimit  = 'Please re execute and enter' . PHP_EOL;
$lang->set->noWriteAccess = 'Unable to open config file!' . PHP_EOL;
$lang->set->saveSuccess   = 'Saved successfully.' . PHP_EOL;
$lang->set->urlInvalid    = 'The host %s is invalid, please check again:' . PHP_EOL;
$lang->set->loginFailed   = 'Login failed. Please check your account and password:' . PHP_EOL;

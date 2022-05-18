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
设置禅道配置，请按ctrl+c退出。

请输入禅道目录，例如：/opt/zentao ：

EOF;
$lang->set->urlTip     = '请输入禅道地址： 例如：https://zentaopms.com：' . PHP_EOL;
$lang->set->accountTip = '用户名：' . PHP_EOL;
$lang->set->pwdTip     = '密码：' . PHP_EOL;
$lang->set->checking   = '正在检验网址...' . PHP_EOL;
$lang->set->logging    = '正在登录...' . PHP_EOL;

$lang->set->dirNotExists  = '目录%s不是禅道目录，请重新输入：' . PHP_EOL;
$lang->set->tryTimeLimit  = '请重新运行命令并输入目录信息！' . PHP_EOL;
$lang->set->noWriteAccess = '配置文件不可写!' . PHP_EOL;
$lang->set->saveSuccess   = '保存成功。' . PHP_EOL;
$lang->set->urlInvalid    = '该网址 %s 不存在或不是禅道网址，请重新输入：' . PHP_EOL;
$lang->set->loginFailed   = '登录失败，请验证账号密码后重试：' . PHP_EOL;

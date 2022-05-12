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
$lang->set->dirNotExists  = '目录%s不是禅道目录，请重新输入：' . PHP_EOL;
$lang->set->tryTimeLimit  = '请重新运行命令并输入目录信息！' . PHP_EOL;
$lang->set->noWriteAccess = '配置文件不可写!' . PHP_EOL;
$lang->set->saveSuccess   = '保存成功。' . PHP_EOL;

<?php
/**
 * The index module simplified chinese file of zentaoPHP.
 *
 * The author disclaims copyright to this source code.  In place of
 * a legal notice, here is a blessing:
 *
 *  May you do good and not evil.
 *  May you find forgiveness for yourself and forgive others.
 *  May you share freely, never taking more than you give.
 */
$lang->patch = new stdclass();
$lang->patch->help->patch = <<<EOF
对适合当前禅道版本的补丁包进行查看列表、详细信息和安装操作。

用法
  z patch <command> [options]

命令
  list      -l              列出当前禅道版本的所有可用补丁包。
  view      -v   <patchid>  查看补丁包。
  install   -i   <patchid>  安装补丁包。
  revert    -r   <patchid>  还原已安装的补丁。

EOF;
$lang->patch->help->view = <<<EOF
用法
  z patch view <patchid>

例如
  z patch view 1 查看ID为1的补丁包信息

EOF;

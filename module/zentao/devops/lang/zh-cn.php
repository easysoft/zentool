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
$lang->devops = new stdclass();
$lang->devops->help = new stdClass();
$lang->devops->help->devops = <<<EOF
基于禅道执行devops操作。

用法
  z devops <command> [options]

命令
  mr  <targetbranch>    合并来自两个分支的代码，并通过流水线对其进行测试。
EOF;
$lang->devops->help->mr = <<<EOF
用法
  z [devops] mr <targetbranch>    合并来自两个分支的代码，并通过流水线对其进行测试。

例如
 z mr master
 z devops mr master
EOF;

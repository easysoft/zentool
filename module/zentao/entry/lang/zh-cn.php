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
$lang->index = new stdclass();
$lang->index->help = <<<EOF
欢迎使用极客ZenTools工具。当前应用是禅道（默认）。

您可以使用'z app list'或'z app switch appName'来获取所有应用并切换到其中一个。

应用程序
  'z app list'：           列出可用的应用。
  'z app switch appName':  切换当前应用。

用法
   z [功能] [命令] [参数]

特征
   patch： 安装禅道补丁。
   devops: 执行devops操作。
   set：   显示和更改当前应用程序的配置信息。

使用“z [功能] --help 或 -h”获取相关功能的更多信息。
EOF;

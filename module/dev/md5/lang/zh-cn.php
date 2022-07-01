<?php
$lang->md5 = new stdClass();
$lang->md5->help = new stdClass();
$lang->md5->help->md5 = <<<EOF
计算字符串或指定文件的md5哈希值。

用法
  z md5 <命令> [参数]

命令
  calculate  -c  <字符串 | 文件>     计算字符串或指定文件的md5哈希值。
EOF;
$lang->md5->help->calculate = <<<EOF
用法
  z md5 calculate [参数]

例如
  z md5 calculate
EOF;

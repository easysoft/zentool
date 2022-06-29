<?php
$lang->md5 = new stdClass();
$lang->md5->help = new stdClass();
$lang->md5->help->md5 = <<<EOF
计算字符串或指定文件的md5哈希值。

用法
  z md5 <command> [options]

命令
  calculate  -c  <string | filepath>     计算字符串或指定文件的md5哈希值。
EOF;
$lang->url->help->calculate = <<<EOF
用法
  z md5 calculate [options]

操作
  -b, --binary   如果使用可选参数，则返回长度为16的md5二进制值。

例如
  z md5 calculate -b
EOF;
